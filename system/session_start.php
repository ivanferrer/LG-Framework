<?php

ob_start();
session_cache_limiter('nocache');
session_cache_expire(15);
session_start();
?>