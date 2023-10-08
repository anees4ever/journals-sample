<?php

class Template {
  public static function render($renderFile, $arguiments) {
    include("template-header.php");

    extract($arguiments);
    include($renderFile);

    include("template-footer.php");
  }
}