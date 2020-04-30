<?php

	function getMembership($membership)
	{
		  $membership_value = substr($membership,-1,1);
      return (int)$membership_value;
	}

?>