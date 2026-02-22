<?php
namespace PhpLicenceWatcher\Util;

enum ACCESS: int {
    case ADMIN = 1;
    case ALL_SERVERS = 2;
    case RESTRICTED_SERVERS = 3;
    case NO_ACCESS = PHP_INT_MAX;
}

/** 
 * User authorization class
 *
 * @author Peter Bailie
 */
class auth {
    static string $signon = "";
    static ACCESS $access_level = ACCESS::NO_ACCESS;
    static array $servers = [];

    static public function authorize(): void {
        self::$signon = $_SERVER['user_auth'] ?? $_SERVER['php_auth'] ?? "";

        // Do database lookup for $signon.
        // If found, set self::$access_level and $servers
        // If not found, set self::$access = ADMIN::NO_ACCESS and $servers = [] and quit.
    }

    static public function access(ACCESS $required = ACCESS::ADMIN, int $server_id = 0): bool {
        switch (true) {
            // ACCESS enums are inverted, meaning lower values have more authority.
            // Therefore, access is granted when $access_level subceeds or equals $required.
            case self::$access_level->value <= $required->value:
                // ACCESS::RESTRICTED_SERVERS also requires a check as this level may only view licenses of specific servers.
                // Therefore user is authorized if the $server_id is found in $servers.
                if (self::$access_level === ACCESS::RESTRICTED_SERVERS)
                    return in_array($server_id, self::$servers);
            
                // Otherwise, it's good.
                return true;

            default:
                return false;
        }           
    }
}

// EOF
