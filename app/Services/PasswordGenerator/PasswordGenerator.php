<?php

namespace App\Services\PasswordGenerator;

use App\Services\PasswordGenerator\Exceptions\InvalidStrengthException;
use App\Services\PasswordGenerator\Exceptions\InvalidLengthException;

class PasswordGenerator
{
    /**
     * @var int
     */
    private int $length;

    /**
     * @var int
     */
    private int $strength;

    /**
     * @var array
     */
    private array $letters;

    /**
     * @var string
     */
    const SPECIALSYMBOLSSET = '!#$%&(){}[]=';

    /**
     * Minimal required length of password
     */
    const MINLENGTH = 6;


    private array $restrictions = [
        'uppercaseLetters' => 1,
        'lowercaseLetters' => 2,
        'numbers'          => 1,
        'specialSymbols'   => 1
    ];

    private array $strengthRestrictions = [
        1 => ['uppercaseLetters', 'lowercaseLetters'],
        2 => ['uppercaseLetters', 'lowercaseLetters', 'numbers'],
        3 => ['uppercaseLetters', 'lowercaseLetters', 'numbers', 'specialSymbols']
    ];

    /**
     *
     */
    const ALLOWEDSTRENGTHS = [1, 2, 3];

    /**
     * @param int $length
     * @param int $strength
     * @throws InvalidLengthException
     * @throws InvalidStrengthException
     */
    public function __construct(int $length, int $strength)
    {
        $this->setLength($length);
        $this->setStrength($strength);
        $this->letters = range('a', 'z');
    }


    /**
     * recursive function
     * generate array of numbers of all needed symbols
     *
     * @throws \Exception
     */
    private function getArrayNumbersOfSymbols(&$initNumbers = []): void
    {
        $sumOfRestrictions = 0;
        if (array_sum($initNumbers) != $this->length) {
            foreach ($this->strengthRestrictions as $key => $strengthRestrictionArr) {
                if ($this->strength == $key) {
                    foreach ($strengthRestrictionArr as $strengthRestriction) {
                        if (!isset($initNumbers[$this->restrictions[$strengthRestriction]])) {
                            $sumOfRestrictions += $this->restrictions[$strengthRestriction];
                        }
                    }
                    foreach ($strengthRestrictionArr as $strengthRestriction) {
                        if (!isset($initNumbers[$strengthRestriction])) {
                            if (count($initNumbers) == count($strengthRestrictionArr) - 1) {
                                $initNumbers[$strengthRestriction] = $this->length - array_sum($initNumbers);
                            } else {
                                $upperLimit = $this->length - $sumOfRestrictions;
                                if ($upperLimit > $this->restrictions[$strengthRestriction]) {
                                    $initNumbers[$strengthRestriction] = random_int($this->restrictions[$strengthRestriction], $upperLimit);
                                } else {
                                    $initNumbers[$strengthRestriction] = $upperLimit;
                                }
                            }
                            break;
                        }
                    }
                }
            }
            $this->getArrayNumbersOfSymbols($initNumbers);
        }
    }

    /**
     * Get generated password
     * @throws \Exception
     * @return string
     */
    public function generate(): string
    {

        $arrOfNumberOfSymbols = [];
        $this->getArrayNumbersOfSymbols($arrOfNumberOfSymbols);
        $password = '';
        foreach ($arrOfNumberOfSymbols as $key => $numberOfSymbol) {
            if ($key == 'lowercaseLetters') {
                for ($i = 0; $i < $numberOfSymbol; $i++) {
                    $password .= $this->getRandomLetter();
                }
            }
            if ($key == 'uppercaseLetters') {
                for ($i = 0; $i < $numberOfSymbol; $i++) {
                    $password .= strtoupper($this->getRandomLetter());
                }
            }

            if ($key == 'numbers') {
                for ($i = 0; $i < $numberOfSymbol; $i++) {
                    $password .= random_int(2, 5);
                }
            }

            if ($key == 'specialSymbols') {
                for ($i = 0; $i < $numberOfSymbol; $i++) {
                    $password .= $this->getRandomSpecialSymbol();
                }
            }

        }

        return str_shuffle($password);
    }


    /**
     * Get a random special symbol
     * @return string
     * @throws \Exception
     */
    private function getRandomSpecialSymbol(): string
    {
        $randomIndex = random_int(0, strlen(self::SPECIALSYMBOLSSET) - 1);
        return self::SPECIALSYMBOLSSET[$randomIndex];
    }

    /**
     * Get a random Letter
     * @return mixed
     * @throws \Exception
     */
    private function getRandomLetter()
    {
        $randomLetterIndex = random_int(0, 25);
        return $this->letters[$randomLetterIndex];
    }

    /**
     * @throws InvalidLengthException
     */
    public function setLength(int $length): void
    {
        if ($length < self::MINLENGTH) {
            throw new InvalidLengthException('The length of the password must be at least 6 characters');
        }
        $this->length = $length;
    }

    /**
     * @throws InvalidStrengthException
     */
    public function setStrength(int $strength): void
    {
        if (!in_array($strength, self::ALLOWEDSTRENGTHS)) {
            throw new InvalidStrengthException('The Password Strength must be defined as 1, 2 or 3');
        }

        $this->strength = $strength;
    }


}
