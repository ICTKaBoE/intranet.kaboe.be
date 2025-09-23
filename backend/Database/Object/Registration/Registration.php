<?php

namespace Database\Object\Registration;

use Database\Interface\CustomObject;
use Database\Repository\Navigation\Navigation;
use Helpers\CString;
use Ouzo\Utilities\Arrays;
use Security\Input;

class Registration extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "guid" => "string",
        "status" => "string",
        "type" => "string",
        "registrationAt" => "datetime",
        "registrationByUserId" => "int",
        "name" => "string",
        "firstName" => "string",
        "callName" => "string",
        "sex" => "string",
        "birthDate" => "date",
        "birthPlace" => "string",
        "birthCountryId" => "int",
        "hasInsz" => "boolean",
        "insz" => "string",
        "nationalityId" => "int",
        "phone" => "string",
        "email" => "string",
        "addressStreet" => "string",
        "addressNumber" => "int",
        "addressBus" => "string",
        "addressZipcode" => "string",
        "addressCity" => "string",
        "addressCountryId" => "int",
        "subscriberRelation" => "string",
        "doctorName" => "string",
        "doctorNumber" => "string",
        "personalReligion" => "string",
        "meansOfTransport" => "string",
        "livingWith" => "string",
        "livingWithOther" => "string",
        "diedParent" => "string",
        "livingWithIName" => "string",
        "bankaccountName" => "string",
        "bankaccountIban" => "string",
        "schoolyearId" => "int",
        "schoolId" => "int",
        "studyyearId" => "int",
        "fieldId" => "int",
        "optionId" => "int",
        "talentId" => "int",
        "clilId" => "int",
        "sitWith" => "string",
        "sitNotWith" => "string",
        "lastSchool" => "string",
        "startAt" => "date",
        "currentSchoolStudyyear" => "string",
        "currentSchoolField" => "string",
        "currentSchoolName" => "string",
        "currentSchoolStreet" => "string",
        "currentSchoolNumber" => "int",
        "currentSchoolBus" => "string",
        "currentSchoolZipcode" => "string",
        "currentSchoolCity" => "string",
        "currentSchoolCountryId" => "int",
        "mealMonday" => "string",
        "mealTuesday" => "string",
        "mealThursday" => "string",
        "mealFriday" => "string",
        "lastSchoolStudyyearB" => "int",
        "lastSchoolNameB" => "string",
        "lastSchoolCountryB" => "int",
        "lastSchoolZipcodeB" => "string",
        "lastSchoolCityB" => "string",
        "lastSchoolHasCertificateB" => "boolean",
        "lastSchoolAdviceB" => "string",
        "lastSchoolCertificateReceivedB" => "string",
        "lastSchoolBaSoCertificateReceivedB" => "string",
        "lastStudyyearS" => "int",
        "lastSchoolFieldS" => "string",
        "lastSchoolNameS" => "string",
        "lastSchoolCountryS" => "int",
        "lastSchoolZipcodeS" => "string",
        "lastSchoolCityS" => "string",
        "lastSchoolCertificateS" => "string",
        "lastSchoolClauseS" => "string",
        "lastStudyyearH" => "int",
        "lastSchoolFieldH" => "string",
        "lastSchoolNameH" => "string",
        "lastSchoolCountryH" => "int",
        "lastSchoolZipcodeH" => "string",
        "lastSchoolCityH" => "string",
        "picture" => "boolean",
        "classPicture" => "boolean",
        "passInfo" => "boolean",
        "measurement" => "boolean",
        "smartschool" => "boolean",
        "website" => "boolean",
        "socials" => "boolean",
        "newsletter" => "boolean",
        "homeLanguage" => "int",
        "homeLanguageMore" => "string",
        "howLongDutch" => "string",
        "problemLearn" => "boolean",
        "problemFamily" => "boolean",
        "problemHealth" => "boolean",
        "problemLearnProblem" => "string",
        "problemLearnProblemOther" => "string",
        "problemLearnCertificate" => "boolean",
        "problemLearnCertificateReceived" => "string",
        "problemLearnGuidance" => "string",
        "problemLearnExtra" => "string",
        "problemFamilyFamily" => "string",
        "problemFamilyPersonal" => "string",
        "problemHealthNotify" => "string",
        "problemHealthDoDont" => "string",
        "problemHealthMedication" => "boolean",
        "problemHealthMedicationWhat" => "string",
        "problemHealthConsult" => "boolean",
        "problemHealthInternalConsult" => "string",
        "conditions" => "boolean",
        "remarks" => "string",
        "deleted" => "boolean",
    ];

    protected $linkedAttributes = [
        "registrationByUser" => ["registrationByUserId" => \Database\Repository\User\User::class],
        "school" => ['schoolId' => \Database\Repository\School\School::class],
        "schoolyear" => ['schoolyearId' => \Database\Repository\Registration\Schoolyear::class],
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
