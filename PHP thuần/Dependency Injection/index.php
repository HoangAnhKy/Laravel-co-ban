<?php

require 'ModelInjection.php';

$use = new ModelInjection();

$use->getModel()->setUserName("kdev");
echo $use->getModel()->getUsername();