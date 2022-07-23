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

function sessionLogin($user)
{
    sessionSet('userId', $user->id);
    sessionSet('username', $user->name);
    sessionSet('role', $user->role);
}

function sessionLogout()
{
    sessionRemove('userId');
    sessionRemove('username');
    sessionRemove('role');
}

function isGuest(): bool
{
    return !(sessionHas('userId') && sessionHas('userName') && sessionHas('role'));
}

function getUsername(): string
{
    return sessionGet('username');
}

function isAdmin(): bool
{
    return !isGuest() && (sessionGet('role') == 'ADMIN');
}
