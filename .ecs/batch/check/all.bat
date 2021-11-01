:: Run easy-coding-standard (ecs) via this batch file inside your IDE e.g. PhpStorm (Windows only)
:: Install inside PhpStorm the  "Batch Script Support" plugin
cd..
cd..
cd..
cd..
cd..
cd..
:: src
vendor\bin\ecs check vendor/markocupic/sac-event-evaluation/src --config vendor/markocupic/sac-event-evaluation/.ecs/config/default.php
:: tests
vendor\bin\ecs check vendor/markocupic/sac-event-evaluation/tests --config vendor/markocupic/sac-event-evaluation/.ecs/config/default.php
:: legacy
vendor\bin\ecs check vendor/markocupic/sac-event-evaluation/src/Resources/contao --config vendor/markocupic/sac-event-evaluation/.ecs/config/legacy.php
:: templates
vendor\bin\ecs check vendor/markocupic/sac-event-evaluation/src/Resources/contao/templates --config vendor/markocupic/sac-event-evaluation/.ecs/config/template.php
::
cd vendor/markocupic/sac-event-evaluation/.ecs./batch/check
