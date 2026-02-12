<?php
require_once '../config.php';
requireLogin();
if (getUserRole() !== 'agency') {
    redirect('/dashboard/' . getUserRole() . '.php');
}
redirect('/dashboard/agency/index.php');
