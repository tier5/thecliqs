
function music_test_active(index,tab){

    hide = getObj(tab);

    show = getObj(tab+"_inactive");

    if (hide.style.display != "none" && tab != index) {

        hide.style.display = "none";

        show.style.display = "";

    }        

}

function music_get_active(show){

    music_test_active(show,"url");

    music_test_active(show,"html_code");

    music_test_active(show,"bb_code");

}

  function music_get_url(getstr){    
    getObj("result_url").value = getstr;
 }
function url_select_text(input_id){

    input_id.select();

}

function openPopup(url){
    Smoothbox.open(url);
  }
  
function getObj(name) 
{   if (document.getElementById) { return document.getElementById(name); }
            else if (document.all)       { return document.all[name]; }
            else if (document.layers)    { return document.layers[name]; }
}
function showhide(id)
{
    if (document.getElementById)
    {
        obj = document.getElementById(id);
        if (obj.style.display == "none")
        {
            document.getElementById('less').style.display = "none";
            document.getElementById('more').style.display = "block";
        } else 
        {
             document.getElementById('less').style.display = "block";
             document.getElementById('more').style.display = "none";
        }
    }
} 