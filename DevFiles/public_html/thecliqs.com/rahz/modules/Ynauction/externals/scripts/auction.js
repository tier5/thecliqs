function checkConfirm()
{
    var user_id = document.getElementById('user_id').value; 
    var check = document.getElementById('check'); 
    var auction_id = document.getElementById('auction_id').value; 
    if(check.checked)
    {
        var request = new Request.JSON({
                'method' : 'post',
                'url' :  en4.core.baseUrl + 'auction/check-confirm/',
                'data' : {
                    'user_id' : user_id
                },
                'onComplete':function(responseObject)
                {  
                    parent.document.getElementById('confirm_user').value = 1; 
                    parent.bidFeatured(auction_id);
                    parent.Smoothbox.close();
                }
            });
            request.send();  
    }
      else
      {
          //parent.Smoothbox.close();
          alert("You must accept the Term of Service!");
          return false;
      } 
}
function refresh_list(auction_id)
{
    var makeRequest = new Request(
            {
                url: en4.core.baseUrl + 'auction/account/his-bids/auction_id/'+ auction_id,
                onComplete: function (respone){
                 document.getElementById('bids_his').innerHTML = respone;
                }
            }
    )
    makeRequest.send();
}
function goto()
{
    parent.window.open(en4.core.baseUrl + "auction/term-service","","status=yes,resizable=yes,scrollbars=yes,fullscreen=no,titlebar=no,width = 1000,height=600");
    return false;
    //parent.Smoothbox.close();
}