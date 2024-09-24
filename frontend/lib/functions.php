
<?php

$BASE_PATH = '/app';

require(__DIR__ . "/flash_messages.php");


require(__DIR__ . "/safer_echo.php");

require(__DIR__ . "/sanitizers.php");


require(__DIR__ . "/user_helpers.php");


//duplicate email/username
require(__DIR__ . "/duplicate_user_details.php");
//reset session
require(__DIR__ . "/reset_session.php");

require(__DIR__ . "/get_url.php");
require(__DIR__ . "/get_or_create_account.php");
require(__DIR__ . "/refresh_account_balance.php");

require(__DIR__ . "/account_helpers.php");

require(__DIR__ . "/get_columns.php");
require(__DIR__ . "/input_map.php");
require(__DIR__ . "/save_data.php");
require(__DIR__ . "/update_data.php");

require(__DIR__ . "/paginate.php");

require(__DIR__ . "/redirect.php");
?>