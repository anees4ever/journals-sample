<?php

class Journals {
  public function index() {
    App::view("journals/list");
  }

  public function entry() {
    App::view("journals/entry");
  }
}