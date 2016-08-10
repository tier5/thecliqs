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

function music_get_url(getstr)
{    
    getObj("result_url").value = getstr;
}
function url_select_text(input_id){

    input_id.select();
}

function _addPlaylist(songId, userId)
{
    var url = en4.core.baseUrl +  "mp3-music/playlist/append/format/smoothbox/playlist_id/0/song_id/" + songId;
    Smoothbox.open(url);
}

function _rate(songId, userId, star)
{
   var makeRequest = new Request(
            {
                url: en4.core.baseUrl +  "mp3music/album/service?name=votesong&idsong="+ songId + "&vote=" + star + "&user=" + userId,
                onComplete:function(response) {
                  
                }
            }
    )
    makeRequest.send();
}
function _onItemChanged(MusicID){
    var makeRequest = new Request(
            {
                url: en4.core.baseUrl + "mp3music/album/service?name=playscount&idsong="+ MusicID,
            }
    )
    makeRequest.send();
    var makeRequest = new Request(
            {
                url: en4.core.baseUrl + "mp3music/album/song-player-ajax/song_id/"+MusicID,
                onComplete: function (respone)
                {
                 	document.getElementById('cter_popup').innerHTML = respone;
                }
            }
    )
    makeRequest.send();
}

function _changePlayCount(MusicID) {
  var makeRequest = new Request(
    {
        url: en4.core.baseUrl + "mp3music/album/service?name=playscount&idsong="+ MusicID,
    }
  )
  makeRequest.send();
}

function openPopup(url){
    Smoothbox.open(url);
  }
  
function getObj(name) 
{   if (document.getElementById) { return document.getElementById(name); }
            else if (document.all)       { return document.all[name]; }
            else if (document.layers)    { return document.layers[name]; }
}
function showhide(hide, show, obj)
{
    if ($('mp3music_lyric_content').hasClass('rows4'))
    {
        $('mp3music_lyric_content').removeClass('rows4');
        obj.innerHTML = hide;
    } 
    else 
    {
        $('mp3music_lyric_content').addClass('rows4');
         obj.innerHTML = show;
    }
} 