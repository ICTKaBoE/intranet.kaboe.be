<?php

namespace Database\Object\Registration;

use Security\CustomObject;
use Database\Repository\Navigation\Navigation;
use Helpers\CString;
use Ouzo\Utilities\Arrays;
use Security\Input;

class Registration extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "guid" => self::TYPE_GUID,
        "status" => self::TYPE_STRING,
        "type" => self::TYPE_STRING,
        "registrationAt" => self::TYPE_DATETIME,
        "registrationByUserId" => self::TYPE_INTEGER,
        "name" => self::TYPE_STRING,
        "firstName" => self::TYPE_STRING,
        "callName" => self::TYPE_STRING,
        "sex" => self::TYPE_STRING,
        "birthDate" => self::TYPE_DATE,
        "birthPlace" => self::TYPE_STRING,
        "birthCountryId" => self::TYPE_INTEGER,
        "hasInsz" => self::TYPE_BOOLEAN,
        "insz" => self::TYPE_STRING,
        "nationalityId" => self::TYPE_INTEGER,
        "phone" => self::TYPE_STRING,
        "email" => self::TYPE_STRING,
        "addressStreet" => self::TYPE_STRING,
        "addressNumber" => self::TYPE_INTEGER,
        "addressBus" => self::TYPE_STRING,
        "addressZipcode" => self::TYPE_STRING,
        "addressCity" => self::TYPE_STRING,
        "addressCountryId" => self::TYPE_INTEGER,
        "subscriberRelation" => self::TYPE_STRING,
        "doctorName" => self::TYPE_STRING,
        "doctorNumber" => self::TYPE_STRING,
        "personalReligion" => self::TYPE_STRING,
        "meansOfTransport" => self::TYPE_STRING,
        "livingWith" => self::TYPE_STRING,
        "livingWithOther" => self::TYPE_STRING,
        "diedParent" => self::TYPE_STRING,
        "livingWithIName" => self::TYPE_STRING,
        "bankaccountName" => self::TYPE_STRING,
        "bankaccountIban" => self::TYPE_STRING,
        "schoolyearId" => self::TYPE_INTEGER,
        "schoolId" => self::TYPE_INTEGER,
        "studyyearId" => self::TYPE_INTEGER,
        "fieldId" => self::TYPE_INTEGER,
        "optionId" => self::TYPE_INTEGER,
        "talentId" => self::TYPE_INTEGER,
        "clilId" => self::TYPE_INTEGER,
        "sitWith" => self::TYPE_STRING,
        "sitNotWith" => self::TYPE_STRING,
        "lastSchool" => self::TYPE_STRING,
        "startAt" => self::TYPE_DATE,
        "currentSchoolStudyyear" => self::TYPE_STRING,
        "currentSchoolField" => self::TYPE_STRING,
        "currentSchoolName" => self::TYPE_STRING,
        "currentSchoolStreet" => self::TYPE_STRING,
        "currentSchoolNumber" => self::TYPE_INTEGER,
        "currentSchoolBus" => self::TYPE_STRING,
        "currentSchoolZipcode" => self::TYPE_STRING,
        "currentSchoolCity" => self::TYPE_STRING,
        "currentSchoolCountryId" => self::TYPE_INTEGER,
        "mealMonday" => self::TYPE_STRING,
        "mealTuesday" => self::TYPE_STRING,
        "mealThursday" => self::TYPE_STRING,
        "mealFriday" => self::TYPE_STRING,
        "lastSchoolStudyyearB" => self::TYPE_INTEGER,
        "lastSchoolNameB" => self::TYPE_STRING,
        "lastSchoolCountryB" => self::TYPE_INTEGER,
        "lastSchoolZipcodeB" => self::TYPE_STRING,
        "lastSchoolCityB" => self::TYPE_STRING,
        "lastSchoolHasCertificateB" => self::TYPE_BOOLEAN,
        "lastSchoolAdviceB" => self::TYPE_STRING,
        "lastSchoolCertificateReceivedB" => self::TYPE_STRING,
        "lastSchoolBaSoCertificateReceivedB" => self::TYPE_STRING,
        "lastStudyyearS" => self::TYPE_INTEGER,
        "lastSchoolFieldS" => self::TYPE_STRING,
        "lastSchoolNameS" => self::TYPE_STRING,
        "lastSchoolCountryS" => self::TYPE_INTEGER,
        "lastSchoolZipcodeS" => self::TYPE_STRING,
        "lastSchoolCityS" => self::TYPE_STRING,
        "lastSchoolCertificateS" => self::TYPE_STRING,
        "lastSchoolClauseS" => self::TYPE_STRING,
        "lastStudyyearH" => self::TYPE_INTEGER,
        "lastSchoolFieldH" => self::TYPE_STRING,
        "lastSchoolNameH" => self::TYPE_STRING,
        "lastSchoolCountryH" => self::TYPE_INTEGER,
        "lastSchoolZipcodeH" => self::TYPE_STRING,
        "lastSchoolCityH" => self::TYPE_STRING,
        "picture" => self::TYPE_BOOLEAN,
        "classPicture" => self::TYPE_BOOLEAN,
        "passInfo" => self::TYPE_BOOLEAN,
        "measurement" => self::TYPE_BOOLEAN,
        "smartschool" => self::TYPE_BOOLEAN,
        "website" => self::TYPE_BOOLEAN,
        "socials" => self::TYPE_BOOLEAN,
        "newsletter" => self::TYPE_BOOLEAN,
        "homeLanguage" => self::TYPE_INTEGER,
        "homeLanguageMore" => self::TYPE_STRING,
        "howLongDutch" => self::TYPE_STRING,
        "problemLearn" => self::TYPE_BOOLEAN,
        "problemFamily" => self::TYPE_BOOLEAN,
        "problemHealth" => self::TYPE_BOOLEAN,
        "problemLearnProblem" => self::TYPE_STRING,
        "problemLearnProblemOther" => self::TYPE_STRING,
        "problemLearnCertificate" => self::TYPE_BOOLEAN,
        "problemLearnCertificateReceived" => self::TYPE_STRING,
        "problemLearnGuidance" => self::TYPE_STRING,
        "problemLearnExtra" => self::TYPE_STRING,
        "problemFamilyFamily" => self::TYPE_STRING,
        "problemFamilyPersonal" => self::TYPE_STRING,
        "problemHealthNotify" => self::TYPE_STRING,
        "problemHealthDoDont" => self::TYPE_STRING,
        "problemHealthMedication" => self::TYPE_BOOLEAN,
        "problemHealthMedicationWhat" => self::TYPE_STRING,
        "problemHealthConsult" => self::TYPE_BOOLEAN,
        "problemHealthInternalConsult" => self::TYPE_STRING,
        "conditions" => self::TYPE_BOOLEAN,
        "remarks" => self::TYPE_STRING,
        "deleted" => self::TYPE_BOOLEAN,
    ];

    protected $linkedAttributes = [
        "registrationByUser" => ["registrationByUserId" => \Database\Repository\User\User::class],
        "school" => ['schoolId' => \Database\Repository\School\School::class],
        "schoolyear" => ['schoolyearId' => \Database\Repository\General\Schoolyear::class],
        "studyyear" => ['studyyearId' => \Database\Repository\Registration\Studyyear::class],
        "field" => ['fieldId' => \Database\Repository\Registration\Field::class],
        "option" => ['optionId' => \Database\Repository\Registration\Option::class],
        "talent" => ['talentId' => \Database\Repository\Registration\Talent::class],
        "clil" => ["clilId" => \Database\Repository\Registration\CLIL::class]
    ];

    public function init()
    {
        $settings = (new Navigation)->getByParentIdAndLink(0, "registration")->settings;
        $this->formatted->studentFullName = Input::createDisplayName("{{FN}} {{LN}}", $this->firstName, $this->name);
        $this->formatted->studentFullNameReversed = Input::createDisplayName("{{LN}} {{FN}}", $this->firstName, $this->name);

        $this->mapped->type = Arrays::getNestedValue($settings, ["type", $this->type]);
        $this->mapped->sex = Arrays::getNestedValue($settings, ["options", "sex", $this->sex]);
        $this->mapped->subscriberRelation = Arrays::getNestedValue($settings, ['options', "relation"], $this->subscriberRelation);
        $this->mapped->meansOfTransport = Arrays::getNestedValue($settings, ['options', 'transport'], $this->meansOfTransport);
        $this->mapped->personalReligion = Arrays::getNestedValue($settings, ["options", "religion", $this->personalReligion]);
        $this->mapped->livingWith = Arrays::getNestedValue($settings, ["options", "livingWith"], $this->livingWith);
        $this->mapped->diedParent = Arrays::getNestedValue($settings, ["options", "diedParent"], $this->diedParent);
        $this->mapped->lastSchool = Arrays::getNestedValue($settings, ["options", "lastSchool"], $this->lastSchool);
        $this->mapped->mealMonday = Arrays::getNestedValue($settings, ["options", "meal"], $this->mealMonday);
        $this->mapped->mealTuesday = Arrays::getNestedValue($settings, ["options", "meal"], $this->mealTuesday);
        $this->mapped->mealThursday = Arrays::getNestedValue($settings, ["options", "meal"], $this->mealThursday);
        $this->mapped->mealFriday = Arrays::getNestedValue($settings, ["options", "meal"], $this->mealFriday);
        $this->mapped->currentSchoolStudyyear = Arrays::getNestedValue($settings, ['options', 'currentSchoolStudyyear'], $this->currentSchoolStudyyear);
        $this->mapped->lastSchoolStudyyearB = Arrays::getNestedValue($settings, ['options', 'lastSchoolStudyyearB'], $this->lastSchoolStudyyearB);
        $this->mapped->lastSchoolStudyyearS = Arrays::getNestedValue($settings, ['options', 'lastSchoolStudyyearS'], $this->lastSchoolStudyyearS);
        $this->mapped->lastSchoolStudyyearH = Arrays::getNestedValue($settings, ['options', 'lastSchoolStudyyearH'], $this->lastSchoolStudyyearH);
        $this->mapped->lastSchoolCertificateReceivedB = Arrays::getNestedValue($settings, ['options', 'lastSchoolCertificateReceivedB'], $this->lastSchoolCertificateReceivedB);
        $this->mapped->lastSchoolBaSoCertificateReceivedB = Arrays::getNestedValue($settings, ['options', 'lastSchoolBaSoCertificateReceivedB'], $this->lastSchoolBaSoCertificateReceivedB);
        $this->mapped->lastSchoolCertificateS = Arrays::getNestedValue($settings, ['options', 'lastSchoolCertificateS'], $this->lastSchoolCertificateS);
        $this->mapped->howLongDutch = Arrays::getNestedValue($settings, ['options', 'howLongDutch'], $this->howLongDutch);
        $this->mapped->problemLearnProblem = Arrays::getNestedValue($settings, ['options', 'problemLearnProblem'], $this->problemLearnProblem);
        $this->mapped->problemLearnCertificateReceived = Arrays::getNestedValue($settings, ['options', 'problemLearnCertificateReceived'], $this->problemLearnCertificateReceived);
        $this->mapped->problemHealthInternalConsult = Arrays::getNestedValue($settings, ['options', 'problemHealthInternalConsult'], $this->problemHealthInternalConsult);
    }
}
