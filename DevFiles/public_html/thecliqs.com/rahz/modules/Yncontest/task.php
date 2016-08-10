<?php
//wget -O- "http://http://dev.younetco.com/qc/cat/dangth427/?m=lite&name=task&module=yncontest" > /dev/null

if(class_exists('Yncontest_Plugin_Task_Timeout')) 
{ 
	Yncontest_Plugin_Task_Timeout::execute();
}
