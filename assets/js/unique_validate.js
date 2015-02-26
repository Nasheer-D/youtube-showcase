jQuery(document).ready(function($){
$.validator.addMethod('uniqueAttr',function(val,element){
  var data_input = $("form").serialize();
  var unique = true;
  $.ajax({
    type: 'GET',
    url: ajaxurl,
    cache: false,
    async: false,
    data: {action:'emd_check_unique',data_input:data_input, ptype:pagenow,myapp:'yt_scase_com'},
    success: function(response)
    {
      unique = response;
    },
  });
  return unique;
}, unique_vars.msg);
});
