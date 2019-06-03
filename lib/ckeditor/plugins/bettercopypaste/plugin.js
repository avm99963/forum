CKEDITOR.plugins.bettercopypaste = {
	init: function (editor) {
	        editor.on('instanceReady', function (e) {
	            this.document.on('dragstart', function (evt) {
			        var draggedtext = evt.data.$.dataTransfer.getData("text/html");
			        evt.data.$.dataTransfer.setData("text/html",draggedtext);
	            });
	         });      
	    }
};

CKEDITOR.plugins.add( 'bettercopypaste', CKEDITOR.plugins.bettercopypaste );