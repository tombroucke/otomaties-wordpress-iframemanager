import '../scss/main.scss';
import './iframemanager-1.0.0';

window.addEventListener('load', function () {
	var manager = iframemanager();
	manager.run({
	  currLang: 'x',
	  services: {
		youtube: {
		  embedUrl: 'https://www.youtube-nocookie.com/embed/{data-id}',
		  thumbnailUrl: 'https://i3.ytimg.com/vi/{data-id}/hqdefault.jpg',
		  iframe: {
			allow: 'accelerometer; encrypted-media; gyroscope; picture-in-picture; fullscreen;',
		  },
		  cookie: {
			name: 'cc_youtube'
		  },
		  languages: {
			x: {
			  notice: props.l10n_notice.replace('{{serviceName}}', 'youtube.com').replace('{{serviceUrl}}', 'https://www.youtube.com/t/terms'),
			  loadBtn: props.l10n_loadVideo,
			  loadAllBtn: props.l10n_loadAllBtn
			},
		  }
		},
		vimeo: {
		  embedUrl: 'https://player.vimeo.com/video/{data-id}',
		  thumbnailUrl: function (id, setThumbnail) {
			var url = 'https://vimeo.com/api/v2/video/' + id + '.json';
			var xhttp = new XMLHttpRequest();
			xhttp.onreadystatechange = function () {
			  if (this.readyState == 4 && this.status == 200) {
				var src = JSON.parse(this.response)[0].thumbnail_large;
				setThumbnail(src);
			  }
			};
			xhttp.open('GET', url, true);
			xhttp.send();
		  },
		  iframe: {
			allow: 'accelerometer; encrypted-media; gyroscope; picture-in-picture; fullscreen;',
		  },
		  cookie: {
			name: 'cc_vimeo'
		  },
		  languages: {
			x: {
			  notice: props.l10n_notice.replace('{{serviceName}}', 'vimeo.com').replace('{{serviceUrl}}', 'https://vimeo.com/terms'),
			  loadBtn: props.l10n_loadVideo,
			  loadAllBtn: props.l10n_loadAllBtn
			}
		  }
		},
		googleMaps: {
		  embedUrl: 'https://www.google.com/maps/embed?pb={data-id}',
		  thumbnailUrl: props.googleMaps.thumbnailUrl,
		  iframe: {
			  allow : 'picture-in-picture; fullscreen;'
		  },
		  cookie: {
			  name: 'cc_google_maps'
		  },
		  languages: {
			x: {
			  notice: props.l10n_notice.replace('{{serviceName}}', 'maps.google.com').replace('{{serviceUrl}}', 'https://policies.google.com/privacy'),
			  loadBtn: props.l10n_loadMap,
			  loadAllBtn: props.l10n_loadAllBtn
			}
		  }
	  }
	  }
	});
  });
  