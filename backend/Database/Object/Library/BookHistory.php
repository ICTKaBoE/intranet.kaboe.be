<?php

namespace Database\Object\Library;

use Security\CustomObject;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Strings;

class BookHistory extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "bookId" => self::TYPE_INTEGER,
        "lenderUserId" => self::TYPE_INTEGER,
        "receiverUserId" => self::TYPE_INTEGER,
        "lenderType" => self::TYPE_STRING,
        "returnerType" => self::TYPE_STRING,
        "lenderInformatId" => self::TYPE_INTEGER,
        "returnerInformatId" => self::TYPE_INTEGER,
        "lendDateTime" => self::TYPE_DATETIME,
        "returnDateTime" => self::TYPE_DATETIME
    ];

    protected $linkedAttributes = [
        // "book" => ['bookId' => \Database\Repository\Library\Book::class],
        "lenderUser" => ['lenderUserId' => \Database\Repository\User\User::class],
        "receiverUser" => ['receiverUserId' => \Database\Repository\User\User::class],
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
