
function showPreview(event){
  if(event.target.files.length > 0){
    var src = URL.createObjectURL(event.target.files[0]);
    var preview = document.getElementById("file-ip-1-preview");
    preview.src = src;
  }
}

var sendButton= document.getElementById('send-btn');
        var innerDiv= document.getElementById('inner');
        sendButton.addEventListener('click', function(){

            innerDiv.innerHTML=innerDiv.innerHTML+'<div class="outgoing" id="outgoing">'+document.getElementById('input').value+
            '<div class="me" id="me"></div></div>';
            document.getElementById('input').value = "";
        });




