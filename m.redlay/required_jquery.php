$('#menu').hide();
$(window).resize(function()
{
    var Document_width=($(window).width())/2;
    var alert_box_width=$('.alert_box').width()/2;
    var errors_width=$('#errors').width()/2;
    
    $('.alert_box').css('left', Document_width-alert_box_width);
    $('#errors').css('left', Document_width-errors_width);
});