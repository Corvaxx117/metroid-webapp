class HoverEffect {
  constructor(selector = ".hover-effect") {
    this.elements = document.querySelectorAll(selector);
    this.initEvents();
  }

  initEvents() {
    this.elements.forEach((element) => {
      element.addEventListener("mousemove", (e) =>
        this.handleHover(e, element)
      );
    });
  }

  handleHover(event, element) {
    let rect = element.getBoundingClientRect();
    let x = ((event.clientX - rect.left) / rect.width) * 100;
    let y = ((event.clientY - rect.top) / rect.height) * 100;
    element.style.setProperty("--mouse-x", x + "%");
    element.style.setProperty("--mouse-y", y + "%");
  }
}
export default HoverEffect;
