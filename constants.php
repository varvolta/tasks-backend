<?php
class Responses
{
    // Index
    const API_IS_WORKING = 'API is working';

    // Auth
    const USER_CREATED = 'User created';
    const EMAIL_ALREADY_REGISTERED = 'Email already registered';
    const PASSWORD_LENGTH_MUST_BE_MINIMUM_6 = 'Password length must be minimum 6';
    const EMAIL_VALIDATION_FAILED = 'Email validation failed';
    const EMAIL_OR_PASSWORD_IS_MISSING = 'Email or password is missing';
    const EMAIL_OR_PASSWORD_IS_WRONG = 'Email or password is wrong';
    const SESSION_CREATED = 'Session created';

    // Tasks
    const UNEXISTING_OR_EXPIRED_SESSION = 'Unexisting or expired session';
    const TOKEN_NOT_SET = 'Token not set';
    const MISSING_IMPORTANT_FIELDS = 'Missing important fields';
    const TASK_CREATED = 'Task created';
    const TASK_UPDATED = 'Task updated';
    const TASK_DELETED = 'Task deleted';
    const COULD_NOT_UPDATE_TASK = 'Could not update task';
    const COULD_NOT_DELETE_TASK = 'Could not delete task';
}

?>