function vapOpenPopup(link) {
	jQuery.fancybox.open({
		src:  link,
		type: 'iframe',
		iframe: {
			css: {
				width:  '95%',
				height: '95%',
			},
		},
	});
}

function vapOpenModalImage(link) {
	jQuery.fancybox.open({
		src:  link,
		type: 'image',
	});
}