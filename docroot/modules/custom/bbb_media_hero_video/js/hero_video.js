/**
 * @file
 * hero_video.js
 */

(function (Drupal) {
  Drupal.behaviors.heroVideo = {
    attach: function (context, settings) {
      // Trigger video Play/Pause.
      let videoButton = document.querySelector('.hero-video + .site-hero-button');
      let playState = document.querySelector('.site-hero-button');
      if (videoButton != null) {
        // Lets enable the button now that JS is available.
        videoButton.classList.remove('visually-hidden');
        let vid = document.querySelector(".hero-video");
        let play = '<span class="visually-hidden">Video is paused, Click to </span>' + Drupal.t('Play Video');
        let pause = '<span class="visually-hidden">Video is playing, Click to </span>' + Drupal.t('Pause Video');
        videoButton.addEventListener("click", function (e) {
          if (this.innerHTML == play) {
            this.innerHTML = pause;
            playState.classList.remove('pause-video');
            playState.classList.add('play-video');
            vid.play();
          } else {
            this.innerHTML = play;
            playState.classList.add('pause-video');
            playState.classList.remove('play-video');
            vid.pause();
          }
        });
        // Hide the native controls now that JS is available.
        let videos = document.getElementsByTagName('video');
        if (videos.length > 0) {
          for (let video of videos) {
            // Turn OFF controls.
            video.controls = false;
          };
        }
      }
    }
  };

})(Drupal);
