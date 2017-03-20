<?php

$DIR = "\\\\visco.local\\data\\Library\\";


echo 'Free space: ' . (floor(100 * disk_free_space($DIR) / disk_total_space($DIR))) . '%';

?>