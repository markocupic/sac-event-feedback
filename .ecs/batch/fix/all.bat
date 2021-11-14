:: Run easy-coding-standard (ecs) via this batch file inside your IDE e.g. PhpStorm (Windows only)
:: Install inside PhpStorm the  "Batch Script Support" plugin
cd..
cd..
cd..
cd..
cd..
cd..
:: src
vendor\bin\ecs check vendor/markocupic/sac-event-feedback/src --fix --config vendor/markocupic/sac-event-feedback/.ecs/config/default.php
:: tests
vendor\bin\ecs check vendor/markocupic/sac-event-feedback/tests --fix --config vendor/markocupic/sac-event-feedback/.ecs/config/default.php
:: legacy
vendor\bin\ecs check vendor/markocupic/sac-event-feedback/src/Resources/contao --fix --config vendor/markocupic/sac-event-feedback/.ecs/config/legacy.php
:: templates
vendor\bin\ecs check vendor/markocupic/sac-event-feedback/src/Resources/contao/templates --fix --config vendor/markocupic/sac-event-feedback/.ecs/config/template.php
::
cd vendor/markocupic/sac-event-feedback/.ecs./batch/fix
