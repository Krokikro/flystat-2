<?php

namespace App\Filament\Support;

use Illuminate\Database\Eloquent\Model;

/**
 * Role matrix (PRD B1): manager — leads only; editor — content; admin — everything.
 * Resources declare static $roles = ['admin', 'editor'] etc.
 */
trait RoleAccess
{
    public static function allowedRoles(): array
    {
        return static::$roles ?? ['admin'];
    }

    public static function canAccess(): bool
    {
        $u = auth()->user();

        return $u && ($u->hasRole('admin') || $u->hasAnyRole(static::allowedRoles()));
    }

    public static function canViewAny(): bool
    {
        return static::canAccess();
    }

    public static function canCreate(): bool
    {
        return static::canAccess();
    }

    public static function canEdit(Model $record): bool
    {
        return static::canAccess();
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()?->hasRole('admin') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }
}
