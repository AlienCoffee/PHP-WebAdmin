<?php
$command ="select topics";//"insert topic -group 2 -topic 18 ";//;//;//"create task -topic 1 -title test35";//;
$result = sendMessageServer($command);
echo "<pre>";
print_r($result);
echo "</pre>";