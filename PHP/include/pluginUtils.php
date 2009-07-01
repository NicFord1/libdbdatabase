<?php
// Provide function needed for plugins to register themselves into a list
// of all plugins

$numberOfPlugins = 0;
$listOfPlugins = array();
function addPlugin($pluginInstance) {
  global $numberOfPlugins, $listOfPlugins;

  $listOfPlugins[$numberOfPlugins] = $pluginInstance;

  $numberOfPlugins++;
}

// Use reflection to invoke given function on all plugins. Return list
// of returned values.
function performFunctionOnAllPlugins($functionName, $param1, $param2) {
  global $listOfPlugins;

  $returnListSize = 0;

  foreach($listOfPlugins as $plugin) {
    $reflector = new ReflectionObject($plugin);
    if($reflector->hasMethod($functionName)) {
      $returnList[$returnListSize++] = $reflector->getMethod($functionName)->
          invoke($plugin, $param1, $param2);
    }
  }
  return $returnList;
}
?>