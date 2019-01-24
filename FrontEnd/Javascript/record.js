$("img")
  .css("cursor", "move")
  .draggable()
  .resizable();
$("h2, #other-demo-hints, #animation")
  .css("cursor", "move")
  .draggable();

var elementToShare = document.getElementById("elementToShare");
var canvas2d = document.createElement("canvas");
var context = canvas2d.getContext("2d");

canvas2d.width = elementToShare.clientWidth;
canvas2d.height = elementToShare.clientHeight;

canvas2d.style.top = 0;
canvas2d.style.left = 0;
canvas2d.style.zIndex = -1;
canvas2d.style.position = "absolute";
(document.body || document.documentElement).appendChild(canvas2d);

var isRecordingStarted = false;
var isStoppedRecording = false;

(function looper() {
  if (!isRecordingStarted) {
    return setTimeout(looper, 500);
  }

  html2canvas(elementToShare, {
    grabMouse: true,
    onrendered: function(canvas) {
      context.clearRect(0, 0, canvas2d.width, canvas2d.height);
      context.drawImage(canvas, 0, 0, canvas2d.width, canvas2d.height);

      if (isStoppedRecording) {
        return;
      }

      setTimeout(looper, 1);
    }
  });
})();

var recorder = new RecordRTC(canvas2d, {
  type: "canvas"
});

document.getElementById("start").onclick = function() {
  document.getElementById("start").disabled = true;

  isStoppedRecording = false;
  isRecordingStarted = true;

  recorder.startRecording();

  setTimeout(function() {
    document.getElementById("stop").disabled = false;
  }, 1000);
};

function querySelectorAll(selector) {
  return Array.prototype.slice.call(document.querySelectorAll(selector));
}

document.getElementById("stop").onclick = function() {
  this.disabled = true;

  isStoppedRecording = true;

  recorder.stopRecording(function() {
    document.body.innerHTML = "";
    document.body.style =
      "margin: 0; padding: 0;background: black; text-align: center; overflow: hidden;";

    var blob = recorder.getBlob();

    var video = document.createElement("video");
    video.src = URL.createObjectURL(blob);
    video.setAttribute("style", "height: 100%;");
    document.body.appendChild(video);
    video.controls = true;
    video.play();
  });
};

window.onbeforeunload = function() {
  document.getElementById("start").disabled = false;
  document.getElementById("stop").disabled = true;
};
