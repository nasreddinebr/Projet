(function($){
  $(function(){

    $('.button-collapse').sideNav();
    $('.parallax').parallax();

  }); // end of document ready
})(jQuery); // end of jQuery name space

$(document).ready(function() {
    Materialize.updateTextFields();
  });

// Activate modale box
$(document).ready(function(){
  // the "href" attribute of .modal-trigger must specify the modal ID that wants to be triggered
  $('.modal').modal();
});

//Move the form to add a comment to reply to a comment
jQuery(document).ready(function($){
	
	$('.reply').click(function(e){
		e.preventDefault();
		var $form = $('#form-comment');
		var $this = $(this);
		var parent_id = $this.data('id');
		var $comment = $('#comment' + parent_id);
		
		$form.find('label').text('Réppondre à ce commentaire');
		$('#comment_write_parent_id').val(parent_id);
		$comment.after($form);
	})
});
   