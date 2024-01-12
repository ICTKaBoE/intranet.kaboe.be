<?php

namespace Controllers\API;

use Security\User;
use Router\Helpers;
use Security\Input;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Database\Repository\Log;
use Controllers\ApiController;
use Database\Object\Holliday as ObjectHolliday;
use Database\Object\ManagementComputer as ObjectManagementComputer;
use Database\Repository\Reservation;
use Database\Object\Reservation as ObjectReservation;
use Database\Repository\Holliday;
use Database\Repository\ManagementCart;
use Database\Repository\ManagementComputer;
use Database\Repository\ManagementIpad;
use Management\Management;
use Security\Code;

class ReservationController extends ApiController
{
    // GET
    public function getReservation($view, $type, $id = null)
    {
        if ($view == "calendar") {
            $schoolId = Helpers::url()->getParam("schoolId");

            if ($type == "dashboard") {
                $assetId = Helpers::url()->getParam("assetId");
                $assetType = Helpers::url()->getParam("type");
                if ($assetType == "L") {
                    $device = (new ManagementComputer)->get($assetId)[0];
                } else if ($assetType == "I") {
                    $device = (new ManagementIpad)->get($assetId)[0];
                }
                $reservations = (new Reservation)->getByAsset($schoolId, $assetType, $device->cartId, $assetId);
            } else if ($type == "mine") {
                $userId = User::getLoggedInUser()->id;
                $reservations = (new Reservation)->getByTypeSchool($userId, $schoolId);
            } else if ($type == "all") {
                $reservations = (new Reservation)->getByTypeSchool(null, $schoolId);
            }
            $reservations = Arrays::filter($reservations, fn ($r) => !(is_null($r->start) && is_null($r->end)));
            Arrays::each($reservations, fn ($r) => $r->link());

            foreach ($reservations as $reservation) {

                $this->appendToJson(data: [
                    "id" => $reservation->id,
                    "start" => $reservation->start,
                    "end" => $reservation->end,
                    "title" => $reservation->extraInfo . count(explode(";", $reservation->assetId)),
                    "borderColor" => "black",
                    "classNames" => [
                        "bg-{$reservation->typeBackgroundColor}",
                        "text-auto"
                    ]
                ]);
            }
        } else if ($view == 'form') $this->appendToJson(['fields'], Arrays::firstOrNull((new Reservation)->get($id)));
        $this->handle();
    }

    // POST
    public function postReservation($view, $type, $id = null)
    {
        $schoolId = Helpers::input()->post('schoolId')?->getValue();
        $assetType = Helpers::input()->post('type')?->getValue();
        $assetId = Helpers::input()->post('assetId')?->getValue();
        $startDate = Helpers::input()->post('startDate')?->getValue();
        $endDate = Helpers::input()->post('endDate')?->getValue();
        $startTime = Helpers::input()->post('startTime')?->getValue();
        $endTime = Helpers::input()->post('endTime')?->getValue();
        $faction = Helpers::input()->post('faction', false)->getValue();

        $reservationRepo = new Reservation;
        $hollidayRepo = new Holliday;
        $isHolliday = $hollidayRepo->dateContainsHolliday($startDate);


        if ($assetType == "LK") {
            $laptopRepo = new ManagementComputer;
        } else if ($assetType == "IK") {
            $ipadRepo = new ManagementIpad;
        } else if ($assetType == "L") {
            $laptopRepo = new ManagementComputer;
            $cartRepo = new ManagementCart;
        } else if ($assetType == "I") {
            $ipadRepo = new ManagementIpad;
            $cartRepo = new ManagementCart;
        }

        if ($faction !== "delete") {
            foreach (explode(";", $assetId) as $_id) {
                if (!Input::check($schoolId, Input::INPUT_TYPE_INT) || Input::empty($schoolId)) {
                    $this->setValidation("schoolId", "School moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
                    Log::write(type: Log::TYPE_ERROR, description: "School is not filled in");
                }
                if (!Input::check($assetType) || Input::empty($assetType)) {
                    $this->setValidation("type", "Type moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
                    Log::write(type: Log::TYPE_ERROR, description: "AssetType is not filled in");
                }
                if (!Input::check($_id) || Input::empty($_id)) {
                    $this->setValidation("assetId", "AssetId moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
                    Log::write(type: Log::TYPE_ERROR, description: "AssetId is not filled in");
                }
                if (!Input::check($startDate) || Input::empty($startDate)) {
                    $this->setValidation("startDate", "Start datum moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
                    Log::write(type: Log::TYPE_ERROR, description: "StartDate is not filled in");
                }
                if (!Input::check($endDate) || Input::empty($endDate)) {
                    $this->setValidation("endDate", "Eind datum moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
                    Log::write(type: Log::TYPE_ERROR, description: "EndDate is not filled in");
                }
                if (!Input::check($startTime) || Input::empty($startTime)) {
                    $this->setValidation("startTime", "Start tijdstip moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
                    Log::write(type: Log::TYPE_ERROR, description: "StartTime is not filled in");
                }
                if (!Input::check($endTime) || Input::empty($endTime)) {
                    $this->setValidation("endTime", "Eind tijdstip moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
                    Log::write(type: Log::TYPE_ERROR, description: "EndTime is not filled in");
                }

                if ($this->validationIsAllGood()) {
                    if (Clock::at("{$startDate} {$startTime}")->isAfterOrEqualTo(Clock::at("{$endDate} {$endTime}"))) {
                        $this->setValidation("endDate", "Eindtijdstip moet na het starttijdstip liggen", self::VALIDATION_STATE_INVALID);
                        $this->setValidation("endTime", "Eindtijdstip moet na het starttijdstip liggen", self::VALIDATION_STATE_INVALID);
                        Log::write(type: Log::TYPE_ERROR, description: "EndTime must be after startTime");
                    }

                    if (Clock::at("{$startDate} {$startTime}")->isBefore(Clock::at("{$startDate} 08:00:00")) || Clock::at("{$startDate} {$startTime}")->isAfter(Clock::at("{$startDate} 16:00:00"))) {
                        $this->setValidation("startTime", "Starttijd moet tussen 8u en 16u liggen", self::VALIDATION_STATE_INVALID);
                        Log::write(type: Log::TYPE_ERROR, description: "StartTime must be between 8 and 16");
                    }

                    if (Clock::at("{$endDate} {$endTime}")->isBefore(Clock::at("{$endDate} 08:00:00")) || Clock::at("{$endDate} {$endTime}")->isAfter(Clock::at("{$endDate} 16:00:00"))) {
                        $this->setValidation("endTime", "Eindtijd moet tussen 8u en 16u liggen", self::VALIDATION_STATE_INVALID);
                        Log::write(type: Log::TYPE_ERROR, description: "EndTime must be between 8 and 16");
                    }
                    if ($isHolliday) {
                        $this->setValidation("startDate", "Startdatum mag niet in een vakantie/feestdag liggen", self::VALIDATION_STATE_INVALID);
                        $this->setValidation("startTime", "Starttijd mag niet in een vakantie/feestdag liggen", self::VALIDATION_STATE_INVALID);
                        Log::write(type: Log::TYPE_ERROR, description: "StartDate and StartTime must be out of a vacation period");
                    }

                    if (Clock::at("{$startDate} {$startTime}")->isBefore(Clock::now())) {
                        $this->setValidation("startDate", "Startdatum moet in de toekomst liggen", self::VALIDATION_STATE_INVALID);
                        Log::write(type: Log::TYPE_ERROR, description: "StartDate must be in the future");
                    }

                    $timestampStartDate = strtotime($startDate);
                    if (date('D', $timestampStartDate) == "Sat" || date('D', $timestampStartDate) == "Sun") {
                        $this->setValidation("startDate", "Je kan geen reservatie maken in het weekend", self::VALIDATION_STATE_INVALID);
                        Log::write(type: Log::TYPE_ERROR, description: "No reservation in the weekend possible");
                    }

                    $timestampEndDate = strtotime($endDate);
                    if (date('D', $timestampEndDate) == "Sat" || date('D', $timestampEndDate) == "Sun") {
                        $this->setValidation("endDate", "Je kan geen reservatie maken in het weekend", self::VALIDATION_STATE_INVALID);
                        Log::write(type: Log::TYPE_ERROR, description: "No reservation in the weekend possible");
                    }

                    $hasOverlap = $reservationRepo->detectOverlap($startDate, $startTime, $endDate, $endTime, $assetType, $_id, $id);
                    if (!empty($hasOverlap)) {
                        $this->setValidation("startDate", "Je reservatie overlapt met een andere reservatie...", self::VALIDATION_STATE_INVALID);
                        $this->setValidation("startTime", "Je reservatie overlapt met een andere reservatie...", self::VALIDATION_STATE_INVALID);
                        $this->setValidation("endDate", "Je reservatie overlapt met een andere reservatie...", self::VALIDATION_STATE_INVALID);
                        $this->setValidation("endTime", "Je reservatie overlapt met een andere reservatie...", self::VALIDATION_STATE_INVALID);
                        Log::write(type: Log::TYPE_ERROR, description: "The reservation overlaps with another reservation");
                    }

                    if ($assetType == "LK") {
                        $deviceslist = $laptopRepo->getByCart($_id);
                        foreach ($deviceslist as $_device) {
                            $hasOverlap = $reservationRepo->detectOverlap($startDate, $startTime, $endDate, $endTime, "L", $_device->id, $id);
                            if (!empty($hasOverlap)) {
                                $this->setValidation("startDate", "Je reservatie overlapt met een andere reservatie van een solo laptop...", self::VALIDATION_STATE_INVALID);
                                $this->setValidation("startTime", "Je reservatie overlapt met een andere reservatie van een solo laptop...", self::VALIDATION_STATE_INVALID);
                                $this->setValidation("endDate", "Je reservatie overlapt met een andere reservatie van een solo laptop...", self::VALIDATION_STATE_INVALID);
                                $this->setValidation("endTime", "Je reservatie overlapt met een andere reservatie van een solo laptop...", self::VALIDATION_STATE_INVALID);
                                Log::write(type: Log::TYPE_ERROR, description: "The reservation overlaps with another reservation of a solo laptop");
                            }
                        }
                    } else if ($assetType == "IK") {
                        $deviceslist = $ipadRepo->getByCart($_id);
                        foreach ($deviceslist as $_device) {
                            $hasOverlap = $reservationRepo->detectOverlap($startDate, $startTime, $endDate, $endTime, "I", $_device->id, $id);
                            if (!empty($hasOverlap)) {
                                $this->setValidation("startDate", "Je reservatie overlapt met een andere reservatie van een solo ipad...", self::VALIDATION_STATE_INVALID);
                                $this->setValidation("startTime", "Je reservatie overlapt met een andere reservatie van een solo ipad...", self::VALIDATION_STATE_INVALID);
                                $this->setValidation("endDate", "Je reservatie overlapt met een andere reservatie van een solo ipad...", self::VALIDATION_STATE_INVALID);
                                $this->setValidation("endTime", "Je reservatie overlapt met een andere reservatie van een solo ipad...", self::VALIDATION_STATE_INVALID);
                                Log::write(type: Log::TYPE_ERROR, description: "The reservation overlaps with another reservation of a solo ipad");
                            }
                        }
                    } else if ($assetType == "L") {
                        $device = $laptopRepo->get($_id)[0];
                        $laptopCartId = $device->cartId;
                        $cart = $cartRepo->get($laptopCartId)[0];
                        if (!is_null($cart->name)) {
                            $hasOverlap = $reservationRepo->detectOverlap($startDate, $startTime, $endDate, $endTime, "LK", $cart->id, $_id);
                            if (!empty($hasOverlap)) {
                                $this->setValidation("startDate", "Je reservatie overlapt met een andere reservatie van een laptopkar...", self::VALIDATION_STATE_INVALID);
                                $this->setValidation("startTime", "Je reservatie overlapt met een andere reservatie van een laptopkar...", self::VALIDATION_STATE_INVALID);
                                $this->setValidation("endDate", "Je reservatie overlapt met een andere reservatie van een laptopkar...", self::VALIDATION_STATE_INVALID);
                                $this->setValidation("endTime", "Je reservatie overlapt met een andere reservatie van een laptopkar...", self::VALIDATION_STATE_INVALID);
                                Log::write(type: Log::TYPE_ERROR, description: "The reservation overlaps with another reservation of a laptopcart");
                            }
                        }
                    } else if ($assetType == "I") {
                        $device = $ipadRepo->get($_id)[0];
                        $ipadCartId = $device->cartId;
                        $cart = $cartRepo->get($ipadCartId)[0];
                        if (!is_null($cart->name)) {
                            $hasOverlap = $reservationRepo->detectOverlap($startDate, $startTime, $endDate, $endTime, "IK", $cart->id, $_id);
                            if (!empty($hasOverlap)) {
                                $this->setValidation("startDate", "Je reservatie overlapt met een andere reservatie van een ipadkar...", self::VALIDATION_STATE_INVALID);
                                $this->setValidation("startTime", "Je reservatie overlapt met een andere reservatie van een ipadkar...", self::VALIDATION_STATE_INVALID);
                                $this->setValidation("endDate", "Je reservatie overlapt met een andere reservatie van een ipadkar...", self::VALIDATION_STATE_INVALID);
                                $this->setValidation("endTime", "Je reservatie overlapt met een andere reservatie van een ipadkar...", self::VALIDATION_STATE_INVALID);
                                Log::write(type: Log::TYPE_ERROR, description: "The reservation overlaps with another reservation of an ipadcart");
                            }
                        }
                    }

                    $spansMoreThenOneDay = !Strings::equal(Clock::at($startDate)->format("Y-m-d"), Clock::at($endDate)->format("Y-m-d"));
                    if ($spansMoreThenOneDay) {
                        $this->setValidation("startDate", "Je kan geen reservatie maken over meerdere dagen", self::VALIDATION_STATE_INVALID);
                        $this->setValidation("endDate", "Je kan geen reservatie maken over meerdere dagen", self::VALIDATION_STATE_INVALID);
                        Log::write(type: Log::TYPE_ERROR, description: "The reservation can not extend over several days");
                    }
                }
            }

            if ($this->validationIsAllGood()) {
                $reservation = is_null($id) ? new ObjectReservation() : $reservationRepo->get($id)[0];
                $reservation->schoolId = $schoolId;
                $reservation->type = $assetType;
                $reservation->assetId = $assetId;
                $reservation->userId = User::getLoggedInUser()->id;
                $reservation->start = "{$startDate} {$startTime}";
                $reservation->end = "{$endDate} {$endTime}";
                $newReservation = $reservationRepo->set($reservation);

                Log::write(description: "Added/Edited reservation with id " . (is_null($id) ? $newReservation : $id));
                if (count(explode(";", $reservation->assetId)) > 1) {
                    if ($reservation->type == "R") {
                        $this->setToast("Reservatie", "De lokalen zijn gereserveerd.");
                    } else {
                        $this->setToast("Reservatie", "De toestellen zijn gereserveerd.");
                    }
                } else {
                    if ($reservation->type == "R") {
                        $this->setToast("Reservatie", "Het lokaal is gereserveerd.");
                    } else {
                        $this->setToast("Reservatie", "Het toestel is gereserveerd.");
                    }
                }
            }
        } else {
            $ids = Helpers::input()->post('ids')->getValue();
            $ids = explode("-", $ids);

            foreach ($ids as $_id) {
                $reservation = Arrays::firstOrNull($reservationRepo->get($_id));

                if (!is_null($reservation)) {
                    if (Clock::at($reservation->start)->isBefore(Clock::now())) {
                        Log::write(type: Log::TYPE_ERROR, description: "Reservations in the past can not be deleted");
                        $this->setToast("Reservatie", "Je kan geen reservatie verwijderen die in het verleden ligt.", self::VALIDATION_STATE_INVALID);
                    } else {
                        $reservation->deleted = 1;
                        $reservationRepo->set($reservation);

                        Log::write(description: "Deleted reservation with id {$reservation->id}");
                        $this->setToast("Reservatie", "De reservatie is verwijderd.");
                    }
                }
            }
        }

        if (!$this->validationIsAllGood()) $this->setHttpCode(400);
        else {
            $this->setCloseModal();
            $this->setReloadTable();
            $this->setReloadCalendar();
        }
        $this->handle();
    }
}
