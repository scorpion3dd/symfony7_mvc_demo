<?php
/**
 * This file is part of the Simple Web Demo Free Lottery Management Application.
 *
 * This project is no longer maintained.
 * The project is written in Symfony Framework Release.
 *
 * @link https://github.com/scorpion3dd
 * @author Denis Puzik <scorpion3dd@gmail.com>
 * @copyright Copyright (c) 2023-2024 scorpion3dd
 */

declare(strict_types=1);

namespace App\Enum;

/**
 * Class CodeAnswer
 * @package App\Enum
 */
class CodeAnswer
{
    const EMPTY_LOGIN_PASSWORD = ['code' => 0, 'message' => 'Empty login and password'];
    const USER_NOT_FOUND = ['code' => 1, 'message' => 'User not found'];
    const ERROR_LOGIN_PASSWORD = ['code' => 2, 'message' => 'Invalid credentials'];
    const EMPTY_TOKEN = ['code' => 3, 'message' => 'Empty token'];
    const REFRESH_TOKEN_NOT_FOUND = ['code' => 4, 'message' => 'Refresh token not found'];
    const ENDPOINT_NOT_FOUND = ['code' => 5, 'message' => 'Endpoint not found'];
    const BAD_REQUEST = ['code' => 6, 'message' => 'Bad request'];
    const UNKNOWN_ERROR = ['code' => 7, 'message' => 'Unknown error'];
    const WRONG_EMAIL = ['code' => 8, 'message' => 'Wrong email'];
    const USER_EXIST = ['code' => 9, 'message' => 'User already exists'];
    const UNAUTHORIZED = ['code' => 10, 'message' => 'Unauthorized'];
    const VALIDATE_ERROR = ['code' => 11, 'message' => 'Validation error'];
    const EMAIL_NOT_CONFIRM = ['code' => 12, 'message' => 'Email are not confirmed'];
    const EMPTY_REFRESH_TOKEN = ['code' => 13, 'message' => 'Empty refresh token'];
    const WRONG_IMAGE = ['code' => 14, 'message' => 'Wrong image'];
    const EMPTY_ID = ['code' => 15, 'message' => 'Empty id'];
    const EMPTY_IMAGE = ['code' => 16, 'message' => 'Empty image'];
    const IP_ADDRESS_NOT_FOUND = ['code' => 17, 'message' => 'IP address was not found'];
    const INVALID_TOKEN = ['code' => 18, 'message' => 'Invalid token'];
    const USER_STATUS_INACTIVE = ['code' => 19, 'message' => 'User in inactive'];
    const DUPLICATION = ['code' => 20, 'message' => 'Duplicate correspondence'];
    const CORRESPONDENCE_EMPTY = ['code' => 21, 'message' => 'Empty correspondence'];
    const CORRESPONDENCE_WRONG = ['code' => 22, 'message' => 'Wrong correspondence'];
    const EMPTY_PASSWORDS = ['code' => 23, 'message' => 'Empty password'];
    const NEW_PASSWORDS_NO_EQUAL = ['code' => 24, 'message' => 'New password mismatch'];
    const OLD_PASSWORD_WRONG = ['code' => 25, 'message' => 'Wrong old password'];
    const BLANK_GREETINGS_EMPTY = ['code' => 26, 'message' => 'Blank greetings are empty'];
    const WRONG_IMAGE_RESOLUTION = ['code' => 27, 'message' => 'Wrong image resolution'];
    const WRONG_IMAGE_EXTENSION = ['code' => 28, 'message' => 'Wrong image extension'];
    const WRONG_IMAGE_SIZE_FILE = ['code' => 29, 'message' => 'Wrong image file size'];
    const BAD_GENDER = ['code' => 30, 'message' => 'Bad gender'];
    const BAD_OFFSET = ['code' => 31, 'message' => 'Bad search offset'];
    const BAD_NAME = ['code' => 32, 'message' => 'Bad name'];
    const EMPTY_REASON_DELETED_ACCOUNT = ['code' => 33, 'message' => 'Empty reason deleted account'];
    const REASON_DELETED_ACCOUNT_NOT_FOUND = ['code' => 34, 'message' => 'Deleted account reason not found'];
    const BAD_REASON_OWN_DELETED_ACCOUNT = ['code' => 35, 'message' => 'Bad reason own deleted account'];
    const BAD_CITY = ['code' => 36, 'message' => 'Bad city'];
    const BAD_AGE = ['code' => 37, 'message' => 'Bad age'];
    const BAD_ZODIAC = ['code' => 38, 'message' => 'Bad zodiac'];
    const BAD_STATUS = ['code' => 39, 'message' => 'Bad status'];
    const PROFILE_NOT_FOUND = ['code' => 40, 'message' => 'Profile not found'];
    const MESSAGE_WRONG = ['code' => 41, 'message' => ''];
    const ONLY_PREMIUM_FEATURE = ['code' => 42, 'message' => 'Only premium feature'];
    const SENDER_IN_BLACKLIST = ['code' => 43, 'message' => 'Sender is in the blacklist'];
    const SURPRISE_NOT_FOUND = ['code' => 44, 'message' => 'Surprise not found'];
    const TICKET_STATUS_NOT_FOUND = ['code' => 45, 'message' => 'Ticket status not found'];
    const TICKET_NOT_FOUND = ['code' => 46, 'message' => 'Ticket not found'];
    const TICKET_POSTED_NOT_AUTHOR = ['code' => 47, 'message' => 'Ticket posted by not the author'];
    const TICKET_CLOSED = ['code' => 48, 'message' => 'Ticket closed'];
    const TICKET_POSTS_NOT_AUTHOR = ['code' => 49, 'message' => 'Ticket posts by not the author'];
    const WRONG_FILE_FORMATE = ['code' => 50, 'message' => 'Wrong file format'];
    const NOT_ENOUGH_COINS = ['code' => 51, 'message' => 'Not enough coins'];
    const CORRESPONDENCE_TO_YOUSELF = ['code' => 52, 'message' => 'Correspondence to yourself'];
    const REASON_DELETE_EMPTY = ['code' => 53, 'message' => 'Empty delete reason'];
    const BAD_PASSWORD = ['code' => 54, 'message' => 'Bad password'];
    const REASON_HIDDEN_ACCOUNT_NOT_FOUND = ['code' => 55, 'message' => 'Account hidden reason not found'];
    const BAD_REASON_OWN_HIDDEN_ACCOUNT = ['code' => 56, 'message' => 'Bad reason own hidden account'];
    const LIMIT_IS_EXCEEDED = ['code' => 57, 'message' => 'Limit is exceeded'];
}
