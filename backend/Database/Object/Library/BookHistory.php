<?php

namespace Database\Object\Library;

use Database\Interface\CustomObject;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Strings;

class BookHistory extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "bookId" => "int",
        "lenderUserId" => "int",
        "receiverUserId" => "int",
        "lenderType" => "string",
        "returnerType" => "string",
        "lenderInformatId" => "int",
        "returnerInformatId" => "int",
        "lendDateTime" => "datetime",
        "returnDateTime" => "datetime"
    ];

    protected $linkedAttributes = [
        "book" => ['bookId' => \Database\Repository\Library\Book::class],
        "lenderUser" => ['lenderUserId' => \Database\Repository\User::class],
        "receiverUser" => ['receiverUserId' => \Database\Repository\User::class],
        "lenderTeacher" => ['lenderInformatId' => \Database\Repository\Informat\Employee::class],
        "returnerTeacher" => ['returnerInformatId' => \Database\Repository\Informat\Employee::class],
        "lenderStudent" => ['lenderInformatId' => \Database\Repository\Informat\Student::class],
        "returnerStudent" => ['returnerInformatId' => \Database\Repository\Informat\Student::class]
    ];

    public function init()
    {
        $this->linked->lender = Strings::equal($this->lenderType, "S") ? $this->linked->lenderStudent : $this->linked->lenderTeacher;
        $this->linked->returner = Strings::equal($this->returnerType, "S") ? $this->linked->returnerStudent : $this->linked->returnerTeacher;

        $this->formatted->lendAt = Clock::at($this->lendDateTime)->format("d/m/Y H:i:s");
        $this->formatted->returnedAt = Clock::at($this->returnDateTime)->format("d/m/Y H:i:s");
    }
}
