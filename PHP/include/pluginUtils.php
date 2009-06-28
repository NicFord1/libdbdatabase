<?php

// Provide function needed for plugins to register themselves into a list
// of all plugins

$numberOfPlugins = 0;
$listOfPlugins = array();
function addPlugin($pluginInstance)
{
  global $numberOfPlugins, $listOfPlugins;

  echo "Adding a plugin!";
  $listOfPlugins[$numberOfPlugins] = $pluginInstance;

  $numberOfPlugins++;

  print("..".$listOfPlugins."..");
}

// Use reflection to invoke given function on all plugins. Return list
// of returned values.
function performFunctionOnAllPlugins($functionName, $param1, $param2)
{
  global $listOfPlugins;

  $returnListSize = 0;
  print("..".$listOfPlugins."..");
  foreach($listOfPlugins as $plugin)
  {
    $reflector = new ReflectionObject($plugin);
    if($reflector->hasMethod($functionName))
    {
      echo "Running search on a plugin!<br>";
      $returnList[$returnListSize++] = $reflector->getMethod($functionName)->
          invoke($plugin, $param1, $param2);
    }
  }
  return $returnList;
}

?>
