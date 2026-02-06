<?php

namespace Controllers\API;

use Helpers\ZIP;
use Helpers\Form;
use Helpers\HTML;
use Security\GUID;
use Security\User;
use Router\Helpers;
use Security\Input;
use Helpers\CString;
use Security\Session;
use Security\FileSystem;
use CloudMersive\Convert;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Controllers\ApiController;
use Database\Repository\Navigation\Navigation;
use PhpOffice\PhpWord\TemplateProcessor;
use Database\Repository\Registration\CLIL;
use Database\Repository\Registration\Field;
use Database\Repository\General\Nationality;
use Database\Repository\Registration\Option;
use Database\Repository\Registration\Talent;
use Database\Repository\Registration\Contact;
use Database\Repository\Registration\Document;
use Database\Repository\Registration\Studyyear;
use Database\Repository\Registration\Schoolyear;
use Database\Repository\Registration\Registration;
use Database\Object\Registration\CLIL as RegistrationCLIL;
use Database\Object\Registration\Field as RegistrationField;
use Database\Object\Registration\Option as RegistrationOption;
use Database\Object\Registration\Talent as RegistrationTalent;
use Database\Object\Registration\Address as RegistrationAddress;
use Database\Object\Registration\Contact as RegistrationContact;
use Database\Object\Registration\Document as RegistrationDocument;
use Database\Object\Registration\Studyyear as RegistrationStudyyear;
use Database\Object\Registration\Schoolyear as RegistrationSchoolyear;
use Database\Object\Registration\Registration as RegistrationRegistration;
use Database\Repository\Informat\ClassGroup;
use Database\Repository\Informat\Registration as InformatRegistration;
use Database\Repository\Informat\RegistrationClass;
use Database\Repository\Registration\Address;
use Database\Repository\School\Institute;
use Helpers\General;
use Karriere\PdfMerge\PdfMerge;

class RegistrationController extends ApiController
{
    const CURRENT_NAVIGATION_MODULE_NAME = "registration";

    const REGISTRATION_FIELDS = [
        1 => [
            "name" => ["mandatory" => true],
            "firstName" =>  ["mandatory" => true],
            "callName",
            "sex" =>  ["mandatory" => true],
            "birthDate" =>  ["mandatory" => true],
            "birthPlace" =>  ["mandatory" => true],
            "birthCountryId" =>  ["mandatory" => true, "type" => Input::INPUT_TYPE_INT],
            "hasInsz",
            "insz" => ["mandatory" => true, "type" => Input::INPUT_TYPE_INSZ, "preconditions" => ["hasInsz" => true], "placeholder" => "__.__.__-___.__", "fieldError" => "Rijksregisternummer heeft niet het correcte formaat!"],
            "nationalityId" => ["type" => Input::INPUT_TYPE_INT],
            "phone" => ["placeholder" => "+32 4__/__.__.__"],
            "email" => ["type" => Input::INPUT_TYPE_EMAIL],
            "addressStreet" =>  ["mandatory" => true],
            "addressNumber" =>  ["mandatory" => true, "type" => Input::INPUT_TYPE_INT],
            "addressBus",
            "addressZipcode" =>  ["mandatory" => true],
            "addressCity" =>  ["mandatory" => true],
            "addressCountryId" =>  ["mandatory" => true, "type" => Input::INPUT_TYPE_INT],
            "subscriberRelation" =>  ["mandatory" => true],
            "doctorName",
            "doctorNumber",
            "meansOfTransport",
            "personalReligion" => ["mandatory" => true]
        ],
        2 => [
            "livingWith" => ["mandatory" => true],
            "livingWithOther" => ["mandatory" => true, "preconditions" => ["livingWith" => "O"]],
            "diedParent",
            "livingWithIName" => ["mandatory" => true, "preconditions" => ["livingWith" => "I"]],
            "bankaccountName",
            "bankaccountIban",
        ],
        3 => [
            "contactOpen1",
            "name1" => ["mandatory" => true, "preconditions" => ["contactOpen1" => true]],
            "firstName1" => ["mandatory" => true, "preconditions" => ["contactOpen1" => true]],
            "relation1" => ["mandatory" => true, "preconditions" => ["contactOpen1" => true]],
            "relationOther1" => ["mandatory" => true, "preconditions" => ["contactOpen1" => true, "relation1" => "O"]],
            "phonePrivate1",
            "phoneWork1",
            "phoneMobile1",
            "email1",
            "contactOpen2",
            "name2" => ["mandatory" => true, "preconditions" => ["contactOpen2" => true]],
            "firstName2" => ["mandatory" => true, "preconditions" => ["contactOpen2" => true]],
            "relation2" => ["mandatory" => true, "preconditions" => ["contactOpen2" => true]],
            "relationOther2" => ["mandatory" => true, "preconditions" => ["contactOpen2" => true, "relation2" => "O"]],
            "phonePrivate2",
            "phoneWork2",
            "phoneMobile2",
            "email2",
            "contactOpen3",
            "name3" => ["mandatory" => true, "preconditions" => ["contactOpen3" => true]],
            "firstName3" => ["mandatory" => true, "preconditions" => ["contactOpen3" => true]],
            "relation3" => ["mandatory" => true, "preconditions" => ["contactOpen3" => true]],
            "relationOther3" => ["mandatory" => true, "preconditions" => ["contactOpen3" => true, "relation3" => "O"]],
            "phonePrivate3",
            "phoneWork3",
            "phoneMobile3",
            "email3",
            "contactOpen4",
            "name4" => ["mandatory" => true, "preconditions" => ["contactOpen4" => true]],
            "firstName4" => ["mandatory" => true, "preconditions" => ["contactOpen4" => true]],
            "relation4" => ["mandatory" => true, "preconditions" => ["contactOpen4" => true]],
            "relationOther4" => ["mandatory" => true, "preconditions" => ["contactOpen4" => true, "relation4" => "O"]],
            "phonePrivate4",
            "phoneWork4",
            "phoneMobile4",
            "email4",
            "contactOpen5",
            "name5" => ["mandatory" => true, "preconditions" => ["contactOpen5" => true]],
            "firstName5" => ["mandatory" => true, "preconditions" => ["contactOpen5" => true]],
            "relation5" => ["mandatory" => true, "preconditions" => ["contactOpen5" => true]],
            "relationOther5" => ["mandatory" => true, "preconditions" => ["contactOpen5" => true, "relation5" => "O"]],
            "phonePrivate5",
            "phoneWork5",
            "phoneMobile5",
            "email5",
            "contactOpen6",
            "name6" => ["mandatory" => true, "preconditions" => ["contactOpen6" => true]],
            "firstName6" => ["mandatory" => true, "preconditions" => ["contactOpen6" => true]],
            "relation6" => ["mandatory" => true, "preconditions" => ["contactOpen6" => true]],
            "relationOther6" => ["mandatory" => true, "preconditions" => ["contactOpen6" => true, "relation6" => "O"]],
            "phonePrivate6",
            "phoneWork6",
            "phoneMobile6",
            "email6",
        ],
        4 => [
            "addressOpen1",
            "copyFrom1",
            "communication1",
            "street1",
            "number1",
            "bus1",
            "zipcode1",
            "city1",
            "country1",
            "addressOpen2",
            "copyFrom2",
            "communication2",
            "street2",
            "number2",
            "bus2",
            "zipcode2",
            "city2",
            "country2",
            "addressOpen3",
            "copyFrom3",
            "communication3",
            "street3",
            "number3",
            "bus3",
            "zipcode3",
            "city3",
            "country3",
            "addressOpen4",
            "copyFrom4",
            "communication4",
            "street4",
            "number4",
            "bus4",
            "zipcode4",
            "city4",
            "country4",
            "addressOpen5",
            "copyFrom5",
            "communication5",
            "street5",
            "number5",
            "bus5",
            "zipcode5",
            "city5",
            "country5",
            "addressOpen6",
            "copyFrom6",
            "communication6",
            "street6",
            "number6",
            "bus6",
            "zipcode6",
            "city6",
            "country6",
        ],
        5 => [
            "schoolyearId" =>  ["mandatory" => true],
            "schoolId" =>  ["mandatory" => true],
            "studyyearId" =>  ["mandatory" => true],
            "fieldId" =>  ["mandatory" => true],
            "optionId",
            "talentId",
            "clilId",
            "sitWith",
            "sitNotWith",
            "lastSchool",
            "mealMonday",
            "mealTuesday",
            "mealThursday",
            "mealFriday",
            "lastSchoolStudyyearB",
            "lastSchoolNameB",
            "lastSchoolCountryB",
            "lastSchoolZipcodeB",
            "lastSchoolCityB",
            "lastSchoolHasCertificateB",
            "lastSchoolAdviceB",
            "lastSchoolCertificateReceivedB",
            "lastSchoolBaSoCertificateReceivedB",
            "lastStudyyearS",
            "lastSchoolFieldS",
            "lastSchoolNameS",
            "lastSchoolCountryS",
            "lastSchoolZipcodeS",
            "lastSchoolCityS",
            "lastSchoolCertificateS",
            "lastSchoolClauseS",
            "lastStudyyearH",
            "lastSchoolFieldH",
            "lastSchoolNameH",
            "lastSchoolCountryH",
            "lastSchoolZipcodeH",
            "lastSchoolCityH"
        ],
        6 => [
            "picture",
            "classPicture",
            "passInfo",
            "measurement",
            "smartschool",
            "website",
            "socials",
            "newsletter",
            "homeLanguage",
            "homeLanguageMore",
            "howLongDutch",
            "problemLearn",
            "problemFamily",
            "problemHealth",
            "problemLearnProblem",
            "problemLearnProblemOther",
            "problemLearnCertificate",
            "problemLearnCertificateReceived",
            "problemLearnGuidance",
            "problemLearnExtra",
            "problemFamilyFamily",
            "problemFamilyPersonal",
            "problemHealthNotify",
            "problemHealthDoDont",
            "problemHealthMedication",
            "problemHealthMedicationWhat",
            "problemHealthConsult",
            "problemHealthInternalConsult"
        ],
        7 => ["conditions", "remarks"]
    ];

    public function getMine($view, $id = null)
    {
        $repo = new Registration;
        $filters = [];
        if (Strings::equal($view, self::VIEW_TABLE)) {

            $this->appendToJson("defaultOrder", [[10, "desc"]]);
            $this->appendToJson(
                key: 'columns',
                data: [
                    [
                        "title" => "School",
                        "data" => "linked.school.formatted.badge.name",
                        "width" => "100px"
                    ],
                    [
                        "title" => "Schooljaar",
                        "data" => "linked.schoolyear.formatted.nameWithCurrent",
                        "width" => "100px"
                    ],
                    [
                        "title" => "Status",
                        "data" => "status",
                        "width" => "100px"
                    ],
                    [
                        "title" => "Leerjaar",
                        "data" => "linked.studyyear.name",
                        "width" => "150px"
                    ],
                    [
                        "title" => "Richting",
                        "data" => "linked.field.name",
                        "width" => "200px"
                    ],
                    [
                        "title" => "Optie",
                        "data" => "linked.option.name",
                        "width" => "100px"
                    ],
                    [
                        "title" => "Talentmodule",
                        "data" => "linked.talent.name",
                        "width" => "100px"
                    ],
                    [
                        "title" => "CLIL",
                        "data" => "linked.clil.name",
                        "width" => "100px"
                    ],
                    [
                        "title" => "Leerling",
                        "data" => "formatted.studentFullName"
                    ],

                    [
                        "title" => "Aangemaakt op",
                        "data" => "registrationAt",
                        "width" => "200px",
                        "className" => "dt-left"
                    ],
                ]
            );

            $items = $repo->get(filters: $filters);
            $this->appendToJson("rows", $items);
        } else if (Strings::equal($view, self::VIEW_FORM)) {
            // $eidData = Session::get('eidData');
            // Session::remove("eidData");

            // if ($eidData) {
            //     $nationalityRepo = new Nationality;

            //     $this->appendToJson('fields', [
            //         'cardNumber' => $eidData->beid_card_number,
            //         'insz' => $eidData->sub,
            //         'birthDate' => $eidData->birthdate,
            //         'addressCity' => $eidData->address->locality,
            //         'addressStreet' => CString::getStreetFromAddress($eidData->address->street_address),
            //         'addressNumber' => preg_replace('/[^0-9]/', '', CString::getHouseNumberFromAddress($eidData->address->street_address)),
            //         'addressBus' => preg_replace('/[^a-zA-Z]/', '', CString::getHouseNumberFromAddress($eidData->address->street_address)),
            //         'addressZipcode' => $eidData->address->postal_code,
            //         'sex' => strtoupper(substr($eidData->gender, 0, 1)),
            //         'chipNumber' => $eidData->beid_chip_number,
            //         'photo' => $eidData->photo,
            //         'type' => $eidData->beid_document_type,
            //         'firstName' => $eidData->given_name,
            //         'middleName' => $eidData->middle_name,
            //         'birthPlace' => $eidData->place_of_birth->locality,
            //         'cardValidUntil' => $eidData->beid_card_validity_end,
            //         'cardDeliveredAt' => $eidData->beid_card_delivery_municipality,
            //         'fullName' => $eidData->name,
            //         'nationalityId' => $nationalityRepo->getByName($eidData->beid_nationality)->id,
            //         'name' => $eidData->family_name,
            //         'age' => $eidData->age,
            //         'cardValidFrom' => $eidData->beid_card_validity_begin
            //     ]);
            // } else $this->appendToJson('fields', []);
        }
    }

    public function getOverview($view, $id = null)
    {
        $repo = new Registration;
        $filters = [];
        if (Strings::equal($view, self::VIEW_TABLE)) {

            $this->appendToJson("defaultOrder", [[10, "desc"]]);
            $this->appendToJson(
                key: 'columns',
                data: [

                    [
                        "title" => "School",
                        "data" => "linked.school.formatted.badge.name",
                        "width" => "100px"
                    ],
                    [
                        "title" => "Schooljaar",
                        "data" => "linked.schoolyear.formatted.nameWithCurrent",
                        "width" => "100px"
                    ],
                    [
                        "title" => "Status",
                        "data" => "status",
                        "width" => "100px"
                    ],
                    [
                        "title" => "Leerjaar",
                        "data" => "linked.studyyear.name",
                        "width" => "150px"
                    ],
                    [
                        "title" => "Richting",
                        "data" => "linked.field.name",
                        "width" => "200px"
                    ],
                    [
                        "title" => "Optie",
                        "data" => "linked.option.name",
                        "width" => "100px"
                    ],
                    [
                        "title" => "Talentmodule",
                        "data" => "linked.talent.name",
                        "width" => "100px"
                    ],
                    [
                        "title" => "CLIL",
                        "data" => "linked.clil.name",
                        "width" => "100px"
                    ],
                    [
                        "title" => "Leerling",
                        "data" => "formatted.studentFullName"
                    ],

                    [
                        "title" => "Aangemaakt op",
                        "data" => "registrationAt",
                        "width" => "200px",
                        "className" => "dt-left"
                    ],
                    [
                        "title" => "Door",
                        "data" => "linked.registrationByUser.formatted.fullNameReversed",
                        "width" => "200px",
                    ],
                ]
            );

            $items = $repo->get(filters: $filters);
            $this->appendToJson("rows", $items);
        }
    }

    public function getSchoolyear($view, $id = null)
    {
        $repo = new Schoolyear;
        $filters = [];

        if (Strings::equal($view, self::VIEW_TABLE)) {

            $this->appendToJson("defaultOrder", [[3, "asc"]]);
            $this->appendToJson(
                key: 'columns',
                data: [

                    [
                        "title" => "Huidig?",
                        "data" => "formatted.icon.current",
                        "width" => "20px",
                        "orderable" => false,
                        "searchable" => false
                    ],
                    [
                        "title" => "Zichtbaar",
                        "data" => "formatted.icon.visible",
                        "width" => "20px",
                        "orderable" => false,
                        "searchable" => false
                    ],
                    [
                        "title" => "Naam",
                        "data" => "name"
                    ]
                ]
            );

            $items = $repo->get(filters: $filters);
            $this->appendToJson("rows", $items);
        } else if (Strings::equal($view, self::VIEW_SELECT)) {
            $items = $repo->get(filters: $filters);
            $items = array_values(Arrays::filter($items, fn($i) => $i->visible));
            $this->appendToJson('items', Arrays::map($items, fn($i) => $i->toArray(true)));
        } else if (Strings::equal($view, self::VIEW_FORM)) $this->appendToJson('fields', $repo->getById($id));
    }

    public function getStudyyear($view, $id = null)
    {
        $repo = new Studyyear;
        $filters = [
            'schoolId' => Arrays::filter(explode(";", Helpers::url()->getParam('schoolId')), fn($i) => Strings::isNotBlank($i)),
            'schoolyearId' => Arrays::filter(explode(";", Helpers::url()->getParam('schoolyearId')), fn($i) => Strings::isNotBlank($i)),
        ];

        if (Strings::equal($view, self::VIEW_TABLE)) {

            $this->appendToJson("defaultOrder", [[2, "asc"], [3, "asc"]]);
            $this->appendToJson(
                key: 'columns',
                data: [

                    [
                        "title" => "School",
                        "data" => "linked.school.formatted.badge.name",
                        "orderable" => false,
                        "searchable" => false,
                        "width" => "100px"
                    ],
                    [
                        "title" => "Schooljaar",
                        "data" => "linked.schoolyear.name",
                        "width" => "100px"
                    ],
                    [
                        "title" => "Naam",
                        "data" => "name"
                    ]
                ]
            );

            $items = $repo->get(filters: $filters);
            $this->appendToJson("rows", $items);
        } else if (Strings::equal($view, self::VIEW_SELECT)) {
            $items = $repo->get(filters: $filters);
            $this->appendToJson('items', Arrays::map($items, fn($i) => $i->toArray(true)));
        } else if (Strings::equal($view, self::VIEW_FORM)) $this->appendToJson('fields', $repo->getById($id));
    }

    public function getField($view, $id = null)
    {
        $repo = new Field;
        $filters = [
            'schoolId' => Arrays::filter(explode(";", Helpers::url()->getParam('schoolId')), fn($i) => Strings::isNotBlank($i)),
            'schoolyearId' => Arrays::filter(explode(";", Helpers::url()->getParam('schoolyearId')), fn($i) => Strings::isNotBlank($i)),
            'studyyearId' => Arrays::filter(explode(";", Helpers::url()->getParam('studyyearId')), fn($i) => Strings::isNotBlank($i)),
        ];

        if (Strings::equal($view, self::VIEW_TABLE)) {
            $this->appendToJson("defaultOrder", [[2, "asc"], [3, "asc"], [4, "asc"]]);
            $this->appendToJson(
                key: 'columns',
                data: [
                    [
                        "title" => "School",
                        "data" => "linked.school.formatted.badge.name",
                        "orderable" => false,
                        "searchable" => false,
                        "width" => "100px"
                    ],
                    [
                        "title" => "Schooljaar",
                        "data" => "linked.schoolyear.name",
                        "width" => "100px"
                    ],
                    [
                        "title" => "Leerjaar",
                        "data" => "linked.studyyear.name",
                        "width" => "200px"
                    ],
                    [
                        "title" => "Naam",
                        "data" => "name"
                    ],
                    [
                        "title" => "Adm. groep",
                        "data" => "administrativeGroupNumber",
                        "width" => "100px"
                    ],
                    [
                        "title" => "Capaciteit",
                        "data" => "maxCapacity",
                        "width" => "100px"
                    ],
                    [
                        "title" => "Zittend",
                        "data" => "linked.sitting",
                        "width" => "100px"
                    ],
                ]
            );

            $items = $repo->get(filters: $filters);

            $instituteRepo = new Institute;
            $classgroupRepo = new ClassGroup;
            $regClassRepo = new RegistrationClass;
            $regRepo = new InformatRegistration;

            foreach ($items as $item) {
                $item->linked->sitting = 0;
                if ($item->id !== 10) continue;
                if (Strings::isBlank($item->administrativeGroupNumber)) continue;

                $schoolyear = $item->linked->schoolyear;
                $institutes = $instituteRepo->getBySchoolId($item->schoolId);

                foreach ($institutes as $institute) {
                    $classgroups = $classgroupRepo->getBySchoolInstituteIdAdministrativeGroupCodeSchoolyearAndType($institute->id, $item->administrativeGroupNumber, $schoolyear->formatted->short, "C");

                    foreach ($classgroups as $classgroup) {
                        $registrationClasses = $regClassRepo->getByInformatClassgroupId($classgroup->id);

                        foreach ($registrationClasses as $rc) {
                            // Gerealiseerd of niet
                            if ($rc->linked->informatRegistration->status !== 0) continue;

                            // Niet dit schooljaar
                            if (Clock::at($rc->linked->informatRegistration->start)->isBefore(Clock::at($schoolyear->start))) continue;
                            if (Clock::at($rc->linked->informatRegistration->end)->isAfter(Clock::at($schoolyear->end))) continue;

                            // Reeds uitgeschreven dit schooljaar
                            // if (!is_null($rc->linked->informatRegistration->end) && Clock::at($rc->linked->informatRegistration->end)->isBefore(Clock::at(Clock::now()))) continue;

                            $item->linked->sitting++;
                        }
                        // $rc->linked->informatRegistration->status == 0
                        // && Clock::at($rc->linked->informatRegistration->start)->isAfterOrEqualTo(Clock::at($schoolyear->start))
                        //     && (
                        //         is_null($rc->linked->informatRegistration->end) ||
                        //         (
                        //             Clock::at($rc->linked->informatRegistration->end)->isBeforeOrEqualTo(Clock::at($schoolyear->end))
                        //             && Clock::at($rc->linked->informatRegistration->end)->isAfterOrEqualTo(Clock::now())
                        //         )
                        //     )
                        // ));
                    }
                }
            }

            $this->appendToJson("rows", $items);
        } else if (Strings::equal($view, self::VIEW_SELECT)) {
            $items = $repo->get(filters: $filters);
            $this->appendToJson('items', Arrays::map($items, fn($i) => $i->toArray(true)));
        } else if (Strings::equal($view, self::VIEW_FORM)) $this->appendToJson('fields', $repo->getById($id));
    }

    public function getOption($view, $id = null)
    {
        $repo = new Option;
        $filters = [
            'schoolId' => Arrays::filter(explode(";", Helpers::url()->getParam('schoolId')), fn($i) => Strings::isNotBlank($i)),
            'schoolyearId' => Arrays::filter(explode(";", Helpers::url()->getParam('schoolyearId')), fn($i) => Strings::isNotBlank($i)),
            'studyyearId' => Arrays::filter(explode(";", Helpers::url()->getParam('studyyearId')), fn($i) => Strings::isNotBlank($i)),
            'fieldId' => Arrays::filter(explode(";", Helpers::url()->getParam('fieldId')), fn($i) => Strings::isNotBlank($i)),
        ];

        if (Strings::equal($view, self::VIEW_TABLE)) {

            $this->appendToJson("defaultOrder", [[2, "asc"], [3, "asc"], [4, "asc"]]);
            $this->appendToJson(
                key: 'columns',
                data: [

                    [
                        "title" => "School",
                        "data" => "linked.school.formatted.badge.name",
                        "orderable" => false,
                        "searchable" => false,
                        "width" => "100px"
                    ],
                    [
                        "title" => "Schooljaar",
                        "data" => "linked.schoolyear.name",
                        "width" => "100px"
                    ],
                    [
                        "title" => "Leerjaar",
                        "data" => "linked.studyyear.name",
                        "width" => "200px"
                    ],
                    [
                        "title" => "Richting",
                        "data" => "linked.field.name",
                        "width" => "300px"
                    ],
                    [
                        "title" => "Naam",
                        "data" => "name"
                    ]
                ]
            );

            $items = $repo->get(filters: $filters);
            $this->appendToJson("rows", $items);
        } else if (Strings::equal($view, self::VIEW_SELECT)) {
            $items = $repo->get(filters: $filters);
            $this->appendToJson('items', Arrays::map($items, fn($i) => $i->toArray(true)));
        } else if (Strings::equal($view, self::VIEW_FORM)) $this->appendToJson('fields', $repo->getById($id));
    }

    public function getTalent($view, $id = null)
    {
        $repo = new Talent;
        $filters = [
            'schoolId' => Arrays::filter(explode(";", Helpers::url()->getParam('schoolId')), fn($i) => Strings::isNotBlank($i)),
            'schoolyearId' => Arrays::filter(explode(";", Helpers::url()->getParam('schoolyearId')), fn($i) => Strings::isNotBlank($i)),
            'studyyearId' => Arrays::filter(explode(";", Helpers::url()->getParam('studyyearId')), fn($i) => Strings::isNotBlank($i)),
            'fieldId' => Arrays::filter(explode(";", Helpers::url()->getParam('fieldId')), fn($i) => Strings::isNotBlank($i)),
        ];

        if (Strings::equal($view, self::VIEW_TABLE)) {

            $this->appendToJson("defaultOrder", [[2, "asc"], [3, "asc"], [4, "asc"]]);
            $this->appendToJson(
                key: 'columns',
                data: [

                    [
                        "title" => "School",
                        "data" => "linked.school.formatted.badge.name",
                        "orderable" => false,
                        "searchable" => false,
                        "width" => "100px"
                    ],
                    [
                        "title" => "Schooljaar",
                        "data" => "linked.schoolyear.name",
                        "width" => "100px"
                    ],
                    [
                        "title" => "Leerjaar",
                        "data" => "linked.studyyear.name",
                        "width" => "200px"
                    ],
                    [
                        "title" => "Richting",
                        "data" => "linked.field.name",
                        "width" => "300px"
                    ],
                    [
                        "title" => "Naam",
                        "data" => "name"
                    ]
                ]
            );

            $items = $repo->get(filters: $filters);
            $this->appendToJson("rows", $items);
        } else if (Strings::equal($view, self::VIEW_SELECT)) {
            $items = $repo->get(filters: $filters);
            $this->appendToJson('items', Arrays::map($items, fn($i) => $i->toArray(true)));
        } else if (Strings::equal($view, self::VIEW_FORM)) $this->appendToJson('fields', $repo->getById($id));
    }

    public function getCLIL($view, $id = null)
    {
        $repo = new CLIL;
        $filters = [
            'schoolId' => Arrays::filter(explode(";", Helpers::url()->getParam('schoolId')), fn($i) => Strings::isNotBlank($i)),
            'schoolyearId' => Arrays::filter(explode(";", Helpers::url()->getParam('schoolyearId')), fn($i) => Strings::isNotBlank($i)),
            'studyyearId' => Arrays::filter(explode(";", Helpers::url()->getParam('studyyearId')), fn($i) => Strings::isNotBlank($i)),
            'fieldId' => Arrays::filter(explode(";", Helpers::url()->getParam('fieldId')), fn($i) => Strings::isNotBlank($i)),
        ];

        if (Strings::equal($view, self::VIEW_TABLE)) {

            $this->appendToJson("defaultOrder", [[2, "asc"], [3, "asc"], [4, "asc"]]);
            $this->appendToJson(
                key: 'columns',
                data: [

                    [
                        "title" => "School",
                        "data" => "linked.school.formatted.badge.name",
                        "orderable" => false,
                        "searchable" => false,
                        "width" => "100px"
                    ],
                    [
                        "title" => "Schooljaar",
                        "data" => "linked.schoolyear.name",
                        "width" => "100px"
                    ],
                    [
                        "title" => "Leerjaar",
                        "data" => "linked.studyyear.name",
                        "width" => "200px"
                    ],
                    [
                        "title" => "Richting",
                        "data" => "linked.field.name",
                        "width" => "300px"
                    ],
                    [
                        "title" => "Naam",
                        "data" => "name"
                    ]
                ]
            );

            $items = $repo->get(filters: $filters);
            $this->appendToJson("rows", $items);
        } else if (Strings::equal($view, self::VIEW_SELECT)) {
            $items = $repo->get(filters: $filters);
            $this->appendToJson('items', Arrays::map($items, fn($i) => $i->toArray(true)));
        } else if (Strings::equal($view, self::VIEW_FORM)) $this->appendToJson('fields', $repo->getById($id));
    }

    public function getOptions($view, $id = null)
    {
        if (Strings::equal($view, self::VIEW_SELECT)) {
            $settings = (new Navigation)->getByParentIdAndLink(0, "registration")->settings;
            $o = Helpers::url()->getParam("o");
            $_items = $settings['options'][$o];
            $items = [];
            foreach ($_items as $k => $v) $items[] = ["id" => $k, "name" => $v];
            $this->appendToJson('items', $items);
        }
    }

    public function getPrint($view, $id = null)
    {
        $repo = new Document;
        $filters = ['type' => 'N'];

        if (Strings::equal($view, self::VIEW_TABLE)) {

            $this->appendToJson("defaultOrder", [[3, "asc"]]);
            $this->appendToJson(
                key: 'columns',
                data: [

                    [
                        "title" => HTML::Icon("file", "Documentstype"),
                        "data" => "formatted.icon.ext",
                        "width" => "20px"
                    ],
                    [
                        "title" => "Type",
                        "data" => "mapped.type",
                        "width" => "200px"
                    ],
                    [
                        "title" => "Alias",
                        "data" => "alias"
                    ]
                ]
            );

            $items = $repo->get(filters: $filters);
            $this->appendToJson("rows", $items);
        } else if (Strings::equal($view, self::VIEW_FORM)) $this->appendToJson('fields', $repo->getById($id));
    }

    public function getUpload($view, $id = null)
    {
        $repo = new Document;
        $filters = [];

        if (Strings::equal($view, self::VIEW_TABLE)) {

            $this->appendToJson("defaultOrder", [[3, "asc"]]);
            $this->appendToJson(
                key: 'columns',
                data: [

                    [
                        "title" => HTML::Icon("file", "Documentstype"),
                        "data" => "formatted.icon.ext",
                        "width" => "20px"
                    ],
                    [
                        "title" => "Type",
                        "data" => "mapped.type",
                        "width" => "200px"
                    ],
                    [
                        "title" => "Volgorde van printen",
                        "data" => "order",
                        "width" => "100px"
                    ],
                    [
                        "title" => "Alias",
                        "data" => "alias"
                    ],
                    [
                        "title" => "Afhankelijk van",
                        "data" => "mapped.dependOn",
                        "width" => "200px"
                    ],
                    [
                        "title" => "Aantal kopieën",
                        "data" => "copies",
                        "width" => "100px"
                    ]
                ]
            );

            $items = $repo->get(filters: $filters);
            $this->appendToJson("rows", $items);
        } else if (Strings::equal($view, self::VIEW_FORM)) $this->appendToJson('fields', $repo->getById($id));
    }

    public function getUploadType($view, $id = null)
    {
        $documentTypes = (new Navigation)->getByParentIdAndLink(0, "registration")->settings['documents']['type'];
        if (Strings::equal($view, self::VIEW_SELECT)) {
            $items = [];
            foreach ($documentTypes as $k => $v) $items[] = ["id" => $k, "name" => $v];
            $this->appendToJson('items', $items);
        }
    }

    // POST
    public function postMine($view, $id = null)
    {
        $_steps = 8;

        // Step Check
        if (Helpers::input()->exists("_step_")) {
            $_step = (int)Helpers::input()->post("_step_")->getValue();
            $_stepDirection = Helpers::input()->post("_stepDirection_")->getValue();

            if ($_stepDirection == "+") {
                [$invalid, $fields] = Form::Validate(self::REGISTRATION_FIELDS[$_step]);
                Arrays::each($invalid, fn($k) => $this->setValidation($k, Arrays::getNestedValue(self::REGISTRATION_FIELDS, [$_step, $k, "fieldError"]), self::VALIDATION_STATE_INVALID));
            }

            if ($this->validationIsAllGood()) {
                $_nextStep = $_stepDirection == "+" ? $_step + 1 : $_step - 1;
                if ($_nextStep < 1) $_nextStep = 1;
                if ($_nextStep > $_steps) $_nextStep = $_steps;

                $this->setActiveStep($_nextStep);
                if ($_nextStep == $_steps) {
                    $this->setActiveButton("Submit");
                    $this->setActiveButton("PrevStep");
                    $this->setNotActiveButton("NextStep");
                    $this->setNotActiveButton("Overview");
                } else if ($_nextStep == $_steps - 1) {
                    $this->setActiveButton("PrevStep");
                    $this->setActiveButton("Overview");
                    $this->setNotActiveButton("Submit");
                    $this->setNotActiveButton("NextStep");
                } else if ($_nextStep == 1) {
                    $this->setActiveButton("NextStep");
                    $this->setNotActiveButton("Submit");
                    $this->setNotActiveButton("PrevStep");
                    $this->setNotActiveButton("Overview");
                } else {
                    $this->setActiveButton("NextStep");
                    $this->setActiveButton("PrevStep");
                    $this->setNotActiveButton("Overview");
                    $this->setNotActiveButton("Submit");
                }
            } else $this->setToast("Gelieve de vereiste velden in te vullen of de fouten te corrigeren!", self::VALIDATION_STATE_INVALID);
        }
        // Global checks
        else {
            $emails = [
                Helpers::input()->post("email")->getValue(),
                Helpers::input()->post("email1")->getValue(),
                Helpers::input()->post("email2")->getValue(),
                Helpers::input()->post("email3")->getValue(),
                Helpers::input()->post("email4")->getValue(),
                Helpers::input()->post("email5")->getValue(),
                Helpers::input()->post("email6")->getValue(),
            ];

            $emails = Arrays::filterNotBlank($emails);

            // Don't allow same emails
            if (count($emails) !== count(array_unique($emails))) {
                $this->setToast("E-mail adressen mogen niet hetzelfde zijn!", self::VALIDATION_STATE_INVALID);
            } else {
                $repo = new Registration;
                $registration = new RegistrationRegistration;

                foreach ($registration->getKeys() as $key) $registration->$key = Helpers::input()->post($key)?->getValue();
                $registration->status = "N";
                $registration->registrationAt = Clock::nowAsString("Y-m-d H:i:s");
                $registration->registrationByUserId = User::getLoggedInUser()->id;

                $nId = $repo->set($registration);
                if (!$id) $registration->id = $nId;
                $registration = $repo->getById($registration->id);

                $contactRepo = new Contact;
                $addressRepo = new Address;
                $emailCount = Input::empty(Helpers::input()->post("email")->getValue()) ? count($emails) : count($emails) - 1;
                for ($c = 1; $c <= $emailCount; $c++) {
                    $contact = new RegistrationContact;
                    $contact->registrationId = $registration->id;
                    $contact->followNumber = $c;
                    $contact->name = Helpers::input()->post("name{$c}")?->getValue();
                    $contact->firstName = Helpers::input()->post("firstName{$c}")?->getValue();
                    $contact->relation = Helpers::input()->post("relation{$c}")?->getValue();
                    $contact->relationOther = Helpers::input()->post("relationOther{$c}")?->getValue();
                    $contact->phonePrivate = Helpers::input()->post("phonePrivate{$c}")?->getValue();
                    $contact->phoneWork = Helpers::input()->post("phoneWork{$c}")?->getValue();
                    $contact->phoneMobile = Helpers::input()->post("phoneMobile{$c}")?->getValue();
                    $contact->email = Helpers::input()->post("email{$c}")?->getValue();
                    $contactRepo->set($contact);

                    $address = new RegistrationAddress;
                    $address->registrationId = $registration->id;
                    $address->followNumber = $c;
                    $address->communication = Helpers::input()->post("communication{$c}")?->getValue();
                    $address->street = Helpers::input()->post("street{$c}")?->getValue();
                    $address->number = Helpers::input()->post("number{$c}")?->getValue();
                    $address->bus = Helpers::input()->post("bus{$c}")?->getValue();
                    $address->zipcode = Helpers::input()->post("zipcode{$c}")?->getValue();
                    $address->city = Helpers::input()->post("city{$c}")?->getValue();
                    $address->countryId = Helpers::input()->post("country{$c}")?->getValue();
                    $addressRepo->set($address);
                }
            }

            if ($this->validationIsAllGood()) $this->setReturn("../");
        }
    }

    public function postSchoolyear($view, $id = null)
    {
        if ($id == "add") $id = null;

        $_fields = [
            "name" => ["mandatory" => true, "placeholder" => "____-____"],
            "visibleFrom" => ["mandatory" => true],
            "visibleUntil" => ["mandatory" => true]
        ];

        [$invalid, $fields] = Form::Validate($_fields);
        Arrays::each($invalid, fn($k) => $this->setValidation($k, Arrays::getNestedValue($_fields, [$k, "fieldError"]), self::VALIDATION_STATE_INVALID));

        if ($this->validationIsAllGood()) {
            $repo = new Schoolyear;

            $item = $repo->getById($id) ?? new RegistrationSchoolyear;
            $item->fillWithPostData();

            $repo->set($item);
        }

        if ($this->validationIsAllGood()) $this->setReturn();
        else $this->setToast("Gelieve de vereiste velden in vullen!", self::VALIDATION_STATE_INVALID);
    }

    public function postStudyyear($view, $id = null)
    {
        if ($id == "add") $id = null;

        $_fields = [
            "schoolId" => ["mandatory" => true, 'type' => Input::INPUT_TYPE_INT],
            "schoolyearId" => ["mandatory" => true, 'type' => Input::INPUT_TYPE_INT],
            "name" => ["mandatory" => true],
        ];

        [$invalid, $fields] = Form::Validate($_fields);
        Arrays::each($invalid, fn($k) => $this->setValidation($k, Arrays::getNestedValue($_fields, [$k, "fieldError"]), self::VALIDATION_STATE_INVALID));

        if ($this->validationIsAllGood()) {
            $repo = new Studyyear;

            $item = $repo->getById($id) ?? new RegistrationStudyyear;
            $item->fillWithPostData();

            $repo->set($item);
        }

        if ($this->validationIsAllGood()) $this->setReturn();
        else $this->setToast("Gelieve de vereiste velden in vullen!", self::VALIDATION_STATE_INVALID);
    }

    public function postField($view, $id = null)
    {
        if ($id == "add") $id = null;

        $_fields = [
            "schoolId" => ["mandatory" => true, 'type' => Input::INPUT_TYPE_INT],
            "schoolyearId" => ["mandatory" => true, 'type' => Input::INPUT_TYPE_INT],
            "studyyearId" => ["mandatory" => true, 'type' => Input::INPUT_TYPE_INT],
            "name" => ["mandatory" => true],
        ];

        [$invalid, $fields] = Form::Validate($_fields);
        Arrays::each($invalid, fn($k) => $this->setValidation($k, Arrays::getNestedValue($_fields, [$k, "fieldError"]), self::VALIDATION_STATE_INVALID));

        if ($this->validationIsAllGood()) {
            $repo = new Field;

            $item = $repo->getById($id) ?? new RegistrationField;
            $item->fillWithPostData();

            $repo->set($item);
        }

        if ($this->validationIsAllGood()) $this->setReturn();
        else $this->setToast("Gelieve de vereiste velden in vullen!", self::VALIDATION_STATE_INVALID);
    }

    public function postOption($view, $id = null)
    {
        if ($id == "add") $id = null;

        $_fields = [
            "schoolId" => ["mandatory" => true, 'type' => Input::INPUT_TYPE_INT],
            "schoolyearId" => ["mandatory" => true, 'type' => Input::INPUT_TYPE_INT],
            "studyyearId" => ["mandatory" => true, 'type' => Input::INPUT_TYPE_INT],
            "fieldId" => ["mandatory" => true, 'type' => Input::INPUT_TYPE_INT],
            "name" => ["mandatory" => true],
        ];

        [$invalid, $fields] = Form::Validate($_fields);
        Arrays::each($invalid, fn($k) => $this->setValidation($k, Arrays::getNestedValue($_fields, [$k, "fieldError"]), self::VALIDATION_STATE_INVALID));

        if ($this->validationIsAllGood()) {
            $repo = new Option;

            $item = $repo->getById($id) ?? new RegistrationOption;
            $item->fillWithPostData();

            $repo->set($item);
        }

        if ($this->validationIsAllGood()) $this->setReturn();
        else $this->setToast("Gelieve de vereiste velden in vullen!", self::VALIDATION_STATE_INVALID);
    }

    public function postTalent($view, $id = null)
    {
        if ($id == "add") $id = null;

        $_fields = [
            "schoolId" => ["mandatory" => true, 'type' => Input::INPUT_TYPE_INT],
            "schoolyearId" => ["mandatory" => true, 'type' => Input::INPUT_TYPE_INT],
            "studyyearId" => ["mandatory" => true, 'type' => Input::INPUT_TYPE_INT],
            "fieldId" => ["mandatory" => true, 'type' => Input::INPUT_TYPE_INT],
            "name" => ["mandatory" => true],
        ];

        [$invalid, $fields] = Form::Validate($_fields);
        Arrays::each($invalid, fn($k) => $this->setValidation($k, Arrays::getNestedValue($_fields, [$k, "fieldError"]), self::VALIDATION_STATE_INVALID));

        if ($this->validationIsAllGood()) {
            $repo = new Talent;

            $item = $repo->getById($id) ?? new RegistrationTalent;
            $item->fillWithPostData();

            $repo->set($item);
        }

        if ($this->validationIsAllGood()) $this->setReturn();
        else $this->setToast("Gelieve de vereiste velden in vullen!", self::VALIDATION_STATE_INVALID);
    }

    public function postCLIL($view, $id = null)
    {
        if ($id == "add") $id = null;

        $_fields = [
            "schoolId" => ["mandatory" => true, 'type' => Input::INPUT_TYPE_INT],
            "schoolyearId" => ["mandatory" => true, 'type' => Input::INPUT_TYPE_INT],
            "studyyearId" => ["mandatory" => true, 'type' => Input::INPUT_TYPE_INT],
            "fieldId" => ["mandatory" => true, 'type' => Input::INPUT_TYPE_INT],
            "name" => ["mandatory" => true],
        ];

        [$invalid, $fields] = Form::Validate($_fields);
        Arrays::each($invalid, fn($k) => $this->setValidation($k, Arrays::getNestedValue($_fields, [$k, "fieldError"]), self::VALIDATION_STATE_INVALID));

        if ($this->validationIsAllGood()) {
            $repo = new CLIL;

            $item = $repo->getById($id) ?? new RegistrationCLIL;
            $item->fillWithPostData();

            $repo->set($item);
        }

        if ($this->validationIsAllGood()) $this->setReturn();
        else $this->setToast("Gelieve de vereiste velden in vullen!", self::VALIDATION_STATE_INVALID);
    }

    public function postUpload($view, $id = null)
    {
        if ($id == "add") $id = null;

        $_fields = [
            "alias" => ["mandatory" => true],
            "type" => ["mandatory" => true],
            "name"
        ];

        [$invalid, $fields] = Form::Validate($_fields);
        Arrays::each($invalid, fn($k) => $this->setValidation($k, Arrays::getNestedValue($_fields, [$k, "fieldError"]), self::VALIDATION_STATE_INVALID));

        if ($this->validationIsAllGood()) {
            $repo = new Document;
            $file = $fields["name"];

            $item = $repo->getById($id) ?? new RegistrationDocument;
            $origName = $item->name;
            $item->fillWithPostData();
            $item->guid = $item->guid ?? GUID::create();

            if ($file && $file[0]->getSize() > 0) {
                $item->name = $file[0]->getFilename();
                $item->ext = $file[0]->getExtension();
                FileSystem::CreateFolder(LOCATION_FILES . "/registration");
                $file[0]->move(LOCATION_FILES . "/registration/{$item->guid}.{$item->ext}");
            } else {
                $item->name = $origName;
            }

            $repo->set($item);
        }

        if ($this->validationIsAllGood()) $this->setReturn();
        else $this->setToast("Gelieve de vereiste velden in vullen!", self::VALIDATION_STATE_INVALID);
    }

    public function postMinePrint($view, $id = null)
    {
        if (!$id) $this->setToast("Geen inschrijving geselecteerd!", self::VALIDATION_STATE_INVALID);

        if ($this->validationIsAllGood()) {
            \PhpOffice\PhpWord\Settings::setOutputEscapingEnabled(true);
            $id = explode("_", $id);
            $repo = new Registration;
            $documents = Arrays::orderBy((new Document)->getByType("R"), "order");

            $settings = (new Navigation)->getByParentIdAndLink(0, "registration")->settings;
            $folder = FileSystem::CreateFolder(LOCATION_FILES . "/registration/" . date("YmdHis"));
            $filename = "Inschrijvingen " . Clock::nowAsString("d-m-Y H-i-s") . ".zip";

            $printVariables = $printChecks = [];

            foreach ($id as $_id) {
                $item = $repo->getById($_id);
                $_folder = $folder . "/{$item->guid}";
                $mergePdf = $folder . "/{$item->guid}.pdf";
                FileSystem::CreateFolder($_folder);

                // Fill printVariables
                Arrays::setNestedValue($printVariables, ['registratie:type'], $item->mapped->type);
                Arrays::setNestedValue($printVariables, ['registratie:voltooid.op'], Clock::at($item->registrationAt)->format("d/m/Y H:i:s"));
                Arrays::setNestedValue($printVariables, ['registratie:voltooid.door'], $item->linked->registrationByUser->formatted->fullNameReversed);
                Arrays::setNestedValue($printVariables, ['school:naam'], $item->linked->school->name);
                Arrays::setNestedValue($printVariables, ['school:naam.metHoofdschool'], $item->linked->school->formatted->nameWithParent);
                Arrays::setNestedValue($printVariables, ['school:adres.straat'], $item->linked->school->street);
                Arrays::setNestedValue($printVariables, ['school:adres.nummer'], $item->linked->school->number);
                Arrays::setNestedValue($printVariables, ['school:adres.bus'], $item->linked->school->bus);
                Arrays::setNestedValue($printVariables, ['school:adres.postcode'], $item->linked->school->zipcode);
                Arrays::setNestedValue($printVariables, ['school:adres.gemeente'], $item->linked->school->city);
                Arrays::setNestedValue($printVariables, ['student:naam'], $item->name);
                Arrays::setNestedValue($printVariables, ['student:voornaam'], $item->firstName);
                Arrays::setNestedValue($printVariables, ['student:adres.straat'], $item->addressStreet);
                Arrays::setNestedValue($printVariables, ['student:adres.nummer'], $item->addressNumber);
                Arrays::setNestedValue($printVariables, ['student:adres.bus'], $item->addressBus);
                Arrays::setNestedValue($printVariables, ['student:adres.postcode'], $item->addressZipcode);
                Arrays::setNestedValue($printVariables, ['student:adres.gemeente'], $item->addressCity);
                Arrays::setNestedValue($printVariables, ['student:geboortedatum'], Clock::at($item->birthDate)->format("d/m/Y"));
                Arrays::setNestedValue($printVariables, ['keuze:schooljaar'], $item->linked->schoolyear->name);
                Arrays::setNestedValue($printVariables, ['keuze:leerjaar'], $item->linked->studyyear->name);
                Arrays::setNestedValue($printVariables, ['keuze:richting'], $item->linked->field->name);
                Arrays::setNestedValue($printVariables, ['keuze:optie'], $item->linked->option->name);
                Arrays::setNestedValue($printVariables, ['keuze:talent'], $item->linked->talent->name);
                Arrays::setNestedValue($printVariables, ['keuze:clil'], $item->linked->clil->name);

                // Fill printChecks
                Arrays::setNestedValue($printChecks, ['status:1'], true);
                Arrays::setNestedValue($printChecks, ['status:2'], false);
                Arrays::setNestedValue($printChecks, ['status:3'], false);

                foreach ($documents as $document) {
                    if (FileSystem::PathExists(LOCATION_FILES . "/registration/{$document->guid}.{$document->ext}")) {
                        // if ($document->fieldId == )
                        if ($document->dependOn) {
                        }
                        $saveAs = $_folder . "/{$document->guid}.{$document->ext}";
                        $saveAsPdf = $_folder . "/{$document->order}_1.pdf";
                        $template = new TemplateProcessor(LOCATION_FILES . "/registration/{$document->guid}.{$document->ext}");

                        foreach ($printVariables as $k => $v) $template->setValue($k, $v);
                        foreach ($printChecks as $k => $v) $template->setCheckbox($k, $v);

                        foreach ($template->getVariables() as $var) $template->setValue($var, '');
                        $template->saveAs($saveAs);

                        $convert = (new Convert)->convert($saveAs, $saveAsPdf); //Convert to PDF
                        if ($convert) {
                            FileSystem::RemoveFile($saveAs); //Remove Word if converted to PDF

                            if ($document->copies > 1) for ($c = 2; $c <= $document->copies; $c++) copy($saveAsPdf, str_replace("_1.pdf", "_{$c}.pdf", $saveAsPdf));
                        }
                    }
                }

                // Merge if 
                if (count(Arrays::filter($mergeFiles = FileSystem::getFiles($_folder), fn($f) => Arrays::last(explode(".", $f)) !== "pdf")) == 0) {
                    $merge = new PdfMerge;
                    foreach ($mergeFiles as $mergeFile) $merge->add("{$_folder}/{$mergeFile}");
                    $merge->merge($mergePdf);
                }
            }

            $zip = new ZIP("{$folder}/{$filename}");
            $zip->addDir($folder);
            $zip->save();

            if ($this->validationIsAllGood()) $this->appendToJson("download", FileSystem::GetDownloadLink("{$folder}/{$filename}"));
            $this->setCloseModal();
        }
    }

    // DELETE
    protected function deleteSchoolyear($view, $id = null)
    {
        $id = explode("_", $id);
        $repo = new Schoolyear;

        foreach ($id as $_id) {
            $item = $repo->getById($_id);
            $attachtedTo = [];

            if (count((new Registration)->getBySchoolyearId($item->id))) $attachtedTo[] = "inschrijvingen";
            if (count((new Studyyear)->getBySchoolyearId($item->id))) $attachtedTo[] = "leerjaren";
            if (count((new Field)->getBySchoolyearId($item->id))) $attachtedTo[] = "studierichtingen";
            if (count((new Option)->getBySchoolyearId($item->id))) $attachtedTo[] = "studieopties";
            if (count((new Talent)->getBySchoolyearId($item->id))) $attachtedTo[] = "talentmodules";
            if (count((new CLIL)->getBySchoolyearId($item->id))) $attachtedTo[] = "CLIL's";

            if (count($attachtedTo)) {
                $this->setToast("Het schooljaar '{$item->name}' kan niet worden verwijderd!<br />Deze is gekoppeld aan " . join(", ", $attachtedTo) . "!", self::VALIDATION_STATE_INVALID);
                continue;
            }

            $item->deleted = 1;
            $repo->set($item);

            $this->setToast("Het schooljaar '{$item->name}' is verwijderd!");
        }

        $this->setReloadTable();
        $this->setCloseModal();
    }

    protected function deleteStudyyear($view, $id = null)
    {
        $id = explode("_", $id);
        $repo = new Studyyear;

        foreach ($id as $_id) {
            $item = $repo->getById($_id);
            $attachtedTo = [];

            if (count((new Registration)->getByStudyyearId($item->id))) $attachtedTo[] = "inschrijvingen";
            if (count((new Field)->getByStudyyearId($item->id))) $attachtedTo[] = "studierichtingen";
            if (count((new Option)->getByStudyyearId($item->id))) $attachtedTo[] = "studieopties";
            if (count((new Talent)->getByStudyyearId($item->id))) $attachtedTo[] = "talentmodules";
            if (count((new CLIL)->getByStudyyearId($item->id))) $attachtedTo[] = "CLIL's";

            if (count($attachtedTo)) {
                $this->setToast("Het leerjaar '{$item->name}' kan niet worden verwijderd!<br />Deze is gekoppeld aan " . join(", ", $attachtedTo) . "!", self::VALIDATION_STATE_INVALID);
                continue;
            }

            $item->deleted = 1;
            $repo->set($item);

            $this->setToast("Het leerjaar '{$item->name}' is verwijderd!");
        }

        $this->setReloadTable();
        $this->setCloseModal();
    }

    protected function deleteField($view, $id = null)
    {
        $id = explode("_", $id);
        $repo = new Field;

        foreach ($id as $_id) {
            $item = $repo->getById($_id);
            $attachtedTo = [];

            if (count((new Registration)->getByFieldId($item->id))) $attachtedTo[] = "inschrijvingen";
            if (count((new Option)->getByFieldId($item->id))) $attachtedTo[] = "studieopties";
            if (count((new Talent)->getByFieldId($item->id))) $attachtedTo[] = "talentmodules";
            if (count((new CLIL)->getByFieldId($item->id))) $attachtedTo[] = "CLIL's";

            if (count($attachtedTo)) {
                $this->setToast("De studierichting '{$item->name}' kan niet worden verwijderd!<br />Deze is gekoppeld aan " . join(", ", $attachtedTo) . "!", self::VALIDATION_STATE_INVALID);
                continue;
            }

            $item->deleted = 1;
            $repo->set($item);

            $this->setToast("De studierichting '{$item->name}' is verwijderd!");
        }

        $this->setReloadTable();
        $this->setCloseModal();
    }

    protected function deleteOption($view, $id = null)
    {
        $id = explode("_", $id);
        $repo = new Option;

        foreach ($id as $_id) {
            $item = $repo->getById($_id);
            $attachtedTo = [];

            if (count((new Registration)->getByOptionId($item->id))) $attachtedTo[] = "inschrijvingen";

            if (count($attachtedTo)) {
                $this->setToast("De studieoptie '{$item->name}' kan niet worden verwijderd!<br />Deze is gekoppeld aan " . join(", ", $attachtedTo) . "!", self::VALIDATION_STATE_INVALID);
                continue;
            }

            $item->deleted = 1;
            $repo->set($item);

            $this->setToast("De studieoptie '{$item->name}' is verwijderd!");
        }

        $this->setReloadTable();
        $this->setCloseModal();
    }

    protected function deleteTalent($view, $id = null)
    {
        $id = explode("_", $id);
        $repo = new Talent;

        foreach ($id as $_id) {
            $item = $repo->getById($_id);
            $attachtedTo = [];

            if (count((new Registration)->getByTalentId($item->id))) $attachtedTo[] = "inschrijvingen";

            if (count($attachtedTo)) {
                $this->setToast("De talentmodule '{$item->name}' kan niet worden verwijderd!<br />Deze is gekoppeld aan " . join(", ", $attachtedTo) . "!", self::VALIDATION_STATE_INVALID);
                continue;
            }

            $item->deleted = 1;
            $repo->set($item);

            $this->setToast("De talentmodule '{$item->name}' is verwijderd!");
        }

        $this->setReloadTable();
        $this->setCloseModal();
    }

    protected function deleteCLIL($view, $id = null)
    {
        $id = explode("_", $id);
        $repo = new CLIL;

        foreach ($id as $_id) {
            $item = $repo->getById($_id);
            $attachtedTo = [];

            if (count((new Registration)->getByCLILId($item->id))) $attachtedTo[] = "inschrijvingen";

            if (count($attachtedTo)) {
                $this->setToast("De CLIL '{$item->name}' kan niet worden verwijderd!<br />Deze is gekoppeld aan " . join(", ", $attachtedTo) . "!", self::VALIDATION_STATE_INVALID);
                continue;
            }

            $item->deleted = 1;
            $repo->set($item);

            $this->setToast("De CLIL '{$item->name}' is verwijderd!");
        }

        $this->setReloadTable();
        $this->setCloseModal();
    }

    protected function deleteUpload($view, $id = null)
    {
        $id = explode("_", $id);
        $repo = new Document;

        foreach ($id as $_id) {
            $item = $repo->getById($_id);
            $item->deleted = 1;
            $repo->set($item);

            $this->setToast("Het document '{$item->alias}' is verwijderd!");
        }

        $this->setReloadTable();
        $this->setCloseModal();
    }
}
