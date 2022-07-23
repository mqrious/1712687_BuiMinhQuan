<?php

session_start();

function sessionSet($key, $value)
{
    $_SESSION[$key] = $value;
}

function sessionGet($key)
{
    return $_SESSION[$key] ?? false;
}

function sessionRemove($key)
{
    if (sessionHas($key)) {
        unset($_SESSION[$key]);
    }
}

function sessionHas($key): bool
{
    return isset($_SESSION[$key]);
}

function sessionLogin(Account $account)
{
    sessionSet('username', $account->username);
    sessionSet('type', $account->type);
}

function sessionLogout()
{
    sessionRemove('username');
    sessionRemove('type');
}

function isGuest(): bool
{
    return !(sessionHas('username') && sessionHas('type'));
}

function getUsername(): string
{
    return sessionGet('username');
}

function isAdmin(): bool
{
    return !isGuest() && (sessionGet('type') == 1);
}
