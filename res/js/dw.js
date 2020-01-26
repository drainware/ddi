
function openCloseDiv(div)
{
  var e = document.getElementById(div); 
  //if(document.getElementById(div).style.visibility!='visible'){
  if(e.style.display == 'none'){
    Effect.SlideDown(div);
  }else{
    Effect.SlideUp(div);
  }
}

function changeOption(form){
  form.gindav[1].checked = true;

}


function selDeSel(state, check)
{
  var mark = state == 'sel' ? 'checked' : '';

  var checkboxes = document.categories.catcheck;
  //var checkboxes = document.categories.cat;

  for(i = 0; i < checkboxes.length; i++){
    checkboxes[i].checked = mark;
  }
}

function selDeSel2(state, check)
{
  var mark = state == 'sel' ? 'checked' : '';

  var checkboxes = document.extensions.catcheck;
  //var checkboxes = document.extensions.cat;

  for(i = 0; i < checkboxes.length; i++){
    checkboxes[i].checked = mark;
  }
}


function changeSelDeSel()
{

  var mark = '';

    if (document.categories.all_categories.checked == 1)
    {
        mark = 'checked';
    }

    var checkboxes = document.categories.catcheck;

    for(i = 0; i < checkboxes.length; i++){
      checkboxes[i].checked = mark;
    }
}




function changeSelDeSel2()
{

  var mark = '';

    if (document.extensions.all_extensions.checked == 1)
    {
        mark = 'checked';
    }

    var checkboxes = document.extensions.extcheck;

    for(i = 0; i < checkboxes.length; i++){
      checkboxes[i].checked = mark;
    }
}


function changeSelDeSel3()
{

  var mark = '';

    if (document.protocols.all_protocols.checked == 1)
    {
	mark = 'checked';
    }
    
    var checkboxes = document.protocols.protocheck;

    for(i = 0; i < checkboxes.length; i++){
      checkboxes[i].checked = mark;
    }
}

