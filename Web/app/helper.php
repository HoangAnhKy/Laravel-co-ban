<?php

if (!function_exists('check_level')) {
    function check_level()
    {
      return session()->get('level') === 1;
    }
  }
