<?

include("html.php");







siteheader();

?>

   <table width="100%" height="100%">

    <tr>

     <td height="12">&nbsp;</td>

    </tr>

    <tr>

     <td valign="top"><TABLE height="100%" width="100%">

       <TBODY>

         <TR>

            <TD colSpan=3 height=12><B><?=$site[name]?> - Game Guide</B></TD>

         </TR>

         <TR></TR>

         <TR>

           <TD width="13%"></TD>

           <TD vAlign=top width="75%">
             <?=$site[guide]?>

                 <A href="guide.php"><FONT 

            color=#ffffff>[</FONT> return to top <FONT 

            color=#ffffff>]</FONT></A>

               </CENTER>

               <BR></TD>

           <TD width="12%"></TD>

         </TR>

       </TBODY>

     </TABLE></td>

    </tr>

   </table>

<?

sitefooter();

?>