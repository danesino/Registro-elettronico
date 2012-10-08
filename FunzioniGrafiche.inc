<?
    function box_errore($message, $ret = FALSE)
    {
        if($ret)
        {
            $a = "<div class=\"box_errore\">$message</div>";
            return $a;
        }
        else
            echo "<div class=\"box_errore\">$message</div>";
    }
    function box_successo($message, $ret = FALSE)
    {
        if($ret)
        {
            $a = "<div class=\"box_successo\">$message</div>";
            return $a;
        }
        else
            exit("<div class=\"box_successo\">$message</div>");
	 }
	 function js_validazione()
	 {
		  print "
				<script type=\"text/javascript\">
function validazione(){
	   if (confirm(\"Sei proprio sicuro di voler cancellare definitivamente?\"))
			   {
					     return true;
						    }
	   else
			   {
					     return false;
						    }
	 }</script>
		  ";
	 }
?>
