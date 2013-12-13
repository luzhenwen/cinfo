<?php
if(!isset($_SESSION['lev']) || $_SESSION['lev'] !== 'manage'){
    exit('Access Deined!');
}