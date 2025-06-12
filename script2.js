// ====================== UTILITY FUNCTIONS ======================
const EditorUtils = {
  getTextNode: function (node) {
    while (node && node.nodeType !== Node.TEXT_NODE && node.firstChild) {
      node = node.firstChild;
    }
    return node;
  },

  getParagraphText: function (node) {
    let parent = node;
    while (
      parent &&
      parent !== document.querySelector(".editor-content") &&
      parent.nodeName !== "P"
    ) {
      parent = parent.parentNode;
    }
    return parent ? parent.textContent : "";
  },

  getParagraphNode: function (node) {
    let parent = node;
    while (
      parent &&
      parent !== document.querySelector(".editor-content") &&
      parent.nodeName !== "P"
    ) {
      parent = parent.parentNode;
    }
    return parent && parent !== document.querySelector(".editor-content")
      ? parent
      : null;
  },

  findParentListItem: function (node) {
    const content = document.querySelector(".editor-content");
    while (node && node !== content) {
      if (node.nodeName === "LI") {
        return node;
      }
      node = node.parentNode;
    }
    return null;
  },

  setCaretPosition: function (element) {
    const range = document.createRange();
    range.selectNodeContents(element);
    range.collapse(false);

    const selection = window.getSelection();
    selection.removeAllRanges();
    selection.addRange(range);
  },

  removeSlashCharacter: function () {
    const selection = window.getSelection();
    if (!selection.rangeCount) return;

    const range = selection.getRangeAt(0);
    const currentNode = this.getTextNode(range.startContainer);
    const text = currentNode.textContent || "";

    const slashPos = text.lastIndexOf("/");
    if (slashPos !== -1) {
      const newRange = document.createRange();
      newRange.setStart(currentNode, slashPos);
      newRange.setEnd(currentNode, slashPos + 1);
      newRange.deleteContents();

      // Update selection
      selection.removeAllRanges();
      selection.addRange(newRange);
    }
  },
};

// ====================== PLACEHOLDER MANAGER ======================
const PlaceholderManager = {
  setup: function (element) {
    element.addEventListener("focus", function () {
      if (this.textContent === "") {
        this.classList.add("placeholder");
      }
    });

    element.addEventListener("blur", function () {
      if (this.textContent === "") {
        this.classList.add("placeholder");
      } else {
        this.classList.remove("placeholder");
      }
    });

    element.addEventListener("input", function () {
      if (this.textContent === "") {
        this.classList.add("placeholder");
      } else {
        this.classList.remove("placeholder");
      }
    });

    if (element.textContent === "") {
      element.classList.add("placeholder");
    }
  },
};

// ====================== WORD COUNT MANAGER ======================
const WordCountManager = {
  init: function () {
    this.wordCountElement = document.querySelector(".word-count");
    this.contentElement = document.querySelector(".editor-content");
    this.update();

    this.contentElement.addEventListener("input", () => this.update());
  },

  update: function () {
    const text = this.contentElement.textContent;
    const words = text.trim() === "" ? 0 : text.trim().split(/\s+/).length;
    this.wordCountElement.textContent = `${words} words`;
  },
};

// ====================== TITLE MANAGER ======================
const TitleManager = {
  init: function () {
    this.titleElement = document.querySelector(".editor-title");
    PlaceholderManager.setup(this.titleElement);

    this.titleElement.addEventListener("keydown", (e) => this.handleKeyDown(e));
  },

  handleKeyDown: function (e) {
    if (e.key === "Enter") {
      e.preventDefault();
      const content = document.querySelector(".editor-content");
      content.focus();

      // If content is empty, add a paragraph
      if (content.textContent === "") {
        const p = document.createElement("p");
        p.innerHTML = "<br>";
        content.appendChild(p);

        // Set cursor inside the paragraph
        EditorUtils.setCaretPosition(p);
      }
    }
  },
};

// ====================== SLASH COMMAND MANAGER ======================
const SlashCommandManager = {
  init: function () {
    this.menu = document.querySelector(".slash-command-menu");
    this.items = document.querySelectorAll(".slash-command-item");
    this.contentElement = document.querySelector(".editor-content");
    this.selectedItemIndex = -1;
    this.isMouseInteraction = false;

    this.setupEventListeners();
  },

  setupEventListeners: function () {
    this.contentElement.addEventListener("input", (e) => this.handleInput(e));
    this.contentElement.addEventListener("keydown", (e) =>
      this.handleKeyDown(e)
    );
    this.contentElement.addEventListener("mousedown", () => {
      this.isMouseInteraction = true;
      this.hideMenu();
    });

    this.items.forEach((item) => {
      item.addEventListener("click", () => {
        this.isMouseInteraction = true;
        this.executeSelectedCommand();
      });
    });
  },

  handleInput: function (e) {
    this.isMouseInteraction = false;
    const selection = window.getSelection();
    if (!selection.rangeCount) return;

    const range = selection.getRangeAt(0);
    const currentNode = EditorUtils.getTextNode(range.startContainer);
    const text = currentNode.textContent || "";
    const cursorPos = range.startOffset;

    // Check if '/' is typed at valid position
    const isSlashValid =
      (cursorPos === 1 && text[0] === "/") || // At start of paragraph
      (text[cursorPos - 1] === "/" &&
        (cursorPos === 1 || text[cursorPos - 2] === "\n")); // After newline

    if (isSlashValid) {
      this.showMenu();
    } else if (this.menu.classList.contains("visible")) {
      // Check if '/' still exists
      const paragraphText = EditorUtils.getParagraphText(currentNode);
      const hasSlash = paragraphText.includes("/");

      if (!hasSlash) {
        this.hideMenu();
      } else {
        // Filter commands
        const slashPos = paragraphText.lastIndexOf("/");
        const inputText = paragraphText.substring(slashPos + 1).toLowerCase();
        this.filterCommands(inputText);
      }
    }
  },

  handleKeyDown: function (e) {
    if (this.menu.classList.contains("visible")) {
      // Handle menu navigation
      if (e.key === "ArrowDown") {
        e.preventDefault();
        this.navigateMenu(1);
      } else if (e.key === "ArrowUp") {
        e.preventDefault();
        this.navigateMenu(-1);
      } else if (e.key === "Enter") {
        e.preventDefault();
        if (!this.isMouseInteraction) {
          this.executeSelectedCommand();
        }
      } else if (e.key === "Escape") {
        e.preventDefault();
        this.hideMenu();
        EditorUtils.removeSlashCharacter();
      }
    }

    // Handle backspace
    if (e.key === "Backspace" && !this.isMouseInteraction) {
      const selection = window.getSelection();
      if (!selection.rangeCount) return;

      const range = selection.getRangeAt(0);
      const currentNode = EditorUtils.getTextNode(range.startContainer);
      const text = currentNode.textContent || "";

      if (
        this.menu.classList.contains("visible") &&
        range.startOffset > 0 &&
        text[range.startOffset - 1] === "/"
      ) {
        this.hideMenu();
      }
    }
  },

  showMenu: function () {
    const selection = window.getSelection();
    if (!selection.rangeCount) return;

    const range = selection.getRangeAt(0);
    const rect = range.getBoundingClientRect();

    this.menu.style.top = `${rect.top + window.pageYOffset + 24}px`;
    this.menu.style.left = `${rect.left + window.pageXOffset}px`;
    this.menu.classList.add("visible");

    // Reset selection
    this.selectedItemIndex = -1;
    this.updateSelectedItem(0);
  },

  hideMenu: function () {
    this.menu.classList.remove("visible");
    this.selectedItemIndex = -1;
  },

  filterCommands: function (filterText) {
    let hasMatches = false;

    this.items.forEach((item, index) => {
      const title = item
        .querySelector(".slash-command-title")
        .textContent.toLowerCase();
      const desc = item
        .querySelector(".slash-command-desc")
        .textContent.toLowerCase();

      if (title.includes(filterText) || desc.includes(filterText)) {
        item.style.display = "flex";
        if (!hasMatches) {
          this.updateSelectedItem(index);
          hasMatches = true;
        }
      } else {
        item.style.display = "none";
      }
    });

    if (!hasMatches) {
      this.selectedItemIndex = -1;
    }
  },

  navigateMenu: function (direction) {
    const visibleItems = Array.from(this.items).filter(
      (item) => item.style.display !== "none"
    );

    if (visibleItems.length === 0) return;

    let newIndex = this.selectedItemIndex + direction;

    if (newIndex < 0) {
      newIndex = visibleItems.length - 1;
    } else if (newIndex >= visibleItems.length) {
      newIndex = 0;
    }

    this.updateSelectedItem(
      Array.from(this.items).indexOf(visibleItems[newIndex])
    );
  },

  updateSelectedItem: function (index) {
    this.items.forEach((item) => {
      item.classList.remove("active");
    });

    if (index >= 0 && index < this.items.length) {
      this.selectedItemIndex = index;
      this.items[index].classList.add("active");
      this.items[index].scrollIntoView({ block: "nearest" });
    }
  },

  executeSelectedCommand: function () {
    if (this.selectedItemIndex === -1 || this.isMouseInteraction) return;

    const selectedItem = this.items[this.selectedItemIndex];
    const command = selectedItem.getAttribute("data-command");

    // Get current paragraph
    const selection = window.getSelection();
    if (!selection.rangeCount) return;

    const range = selection.getRangeAt(0);
    const currentNode = EditorUtils.getTextNode(range.startContainer);
    const paragraph = EditorUtils.getParagraphNode(currentNode);

    // Remove '/' and following text in paragraph
    if (paragraph) {
      const paragraphText = paragraph.textContent;
      const slashIndex = paragraphText.indexOf("/");

      if (slashIndex !== -1) {
        // Find the text node containing /
        let textNode = paragraph;
        let found = false;

        function findSlashNode(node) {
          if (
            node.nodeType === Node.TEXT_NODE &&
            node.textContent.includes("/")
          ) {
            textNode = node;
            found = true;
            return;
          }

          for (let child of node.childNodes) {
            if (!found) findSlashNode(child);
          }
        }

        findSlashNode(paragraph);

        if (found) {
          const slashPos = textNode.textContent.indexOf("/");
          const newRange = document.createRange();
          newRange.setStart(textNode, slashPos);
          newRange.setEnd(paragraph, paragraph.childNodes.length);
          newRange.deleteContents();

          // Execute command
          this.executeCommand(command, paragraph);
        }
      }
    }

    this.hideMenu();
  },

  executeCommand: function (command, paragraph) {
    const selection = window.getSelection();

    switch (command) {
      case "h2":
      case "h3":
        const heading = document.createElement(command);
        heading.textContent = "Heading " + command[1];
        paragraph.parentNode.replaceChild(heading, paragraph);
        EditorUtils.setCaretPosition(heading);
        break;

      case "bullet":
      case "numbered":
        const list = document.createElement(command === "bullet" ? "ul" : "ol");
        const li = document.createElement("li");
        li.textContent = "List item";
        list.appendChild(li);
        paragraph.parentNode.replaceChild(list, paragraph);
        EditorUtils.setCaretPosition(li);
        break;

      case "quote":
        const blockquote = document.createElement("blockquote");
        blockquote.textContent = "Quote";
        paragraph.parentNode.replaceChild(blockquote, paragraph);
        EditorUtils.setCaretPosition(blockquote);
        break;

      case "divider":
        const divider = document.createElement("div");
        divider.className = "divider";

        // Insert divider and new paragraph
        if (paragraph) {
          paragraph.parentNode.insertBefore(divider, paragraph);
          const newP = document.createElement("p");
          newP.innerHTML = "<br>";
          paragraph.parentNode.insertBefore(newP, paragraph);
          paragraph.parentNode.removeChild(paragraph);
          EditorUtils.setCaretPosition(newP);
        } else {
          const range = selection.getRangeAt(0);
          range.insertNode(divider);

          // Add new paragraph after divider
          const p = document.createElement("p");
          p.innerHTML = "<br>";
          range.insertNode(p);

          // Move cursor to new paragraph
          EditorUtils.setCaretPosition(p);
        }
        break;

      case "code":
        const codeBlock = document.createElement("div");
        codeBlock.className = "code-block";
        codeBlock.textContent = "// Your code here";

        if (paragraph) {
          paragraph.parentNode.replaceChild(codeBlock, paragraph);
        } else {
          const range = selection.getRangeAt(0);
          range.insertNode(codeBlock);
        }
        EditorUtils.setCaretPosition(codeBlock);
        break;

      case "image":
        ImageUploadManager.open();
        break;
    }
  },
};

// ====================== PARAGRAPH MANAGER ======================
const ParagraphManager = {
  init: function () {
    this.contentElement = document.querySelector(".editor-content");
    this.contentElement.addEventListener("keydown", (e) => {
      if (
        e.key === "Enter" &&
        !document
          .querySelector(".slash-command-menu")
          .classList.contains("visible")
      ) {
        this.handleEnter(e);
      }
    });
  },

  handleEnter: function (e) {
    const selection = window.getSelection();
    if (!selection.rangeCount) return;

    const range = selection.getRangeAt(0);
    const currentNode = range.startContainer;

    // Check if in list item
    const listItem = EditorUtils.findParentListItem(currentNode);
    if (listItem) {
      const list = listItem.parentNode;
      const isOrderedList = list.tagName === "OL";

      // If list item is empty, exit the list
      if (listItem.textContent.trim() === "") {
        e.preventDefault();

        // Create new paragraph after list
        const newP = document.createElement("p");
        newP.innerHTML = "<br>";

        // If this is the only item, convert list to paragraph
        if (list.childNodes.length === 1) {
          list.parentNode.replaceChild(newP, list);
        } else {
          // Remove empty item and create paragraph after list
          list.removeChild(listItem);
          list.parentNode.insertBefore(newP, list.nextSibling);
        }

        EditorUtils.setCaretPosition(newP);
        return;
      }

      // If in ordered list, calculate next number
      if (isOrderedList) {
        e.preventDefault();

        const newItem = document.createElement("li");
        newItem.innerHTML = "<br>";

        // Calculate next number
        const prevItems = Array.from(list.childNodes);
        const currentIndex = prevItems.indexOf(listItem);
        const nextNumber = currentIndex + 2; // +1 for zero-based, +1 for next

        // Insert new item after current
        if (currentIndex < prevItems.length - 1) {
          list.insertBefore(newItem, prevItems[currentIndex + 1]);
        } else {
          list.appendChild(newItem);
        }

        EditorUtils.setCaretPosition(newItem);
        return;
      } else {
        // For bullet list
        e.preventDefault();

        const newItem = document.createElement("li");
        newItem.innerHTML = "<br>";

        // Insert new item after current
        const prevItems = Array.from(list.childNodes);
        const currentIndex = prevItems.indexOf(listItem);

        if (currentIndex < prevItems.length - 1) {
          list.insertBefore(newItem, prevItems[currentIndex + 1]);
        } else {
          list.appendChild(newItem);
        }

        EditorUtils.setCaretPosition(newItem);
        return;
      }
    }

    // If in empty paragraph, prevent creating extra paragraphs
    if (currentNode.nodeName === "P" && currentNode.textContent === "") {
      e.preventDefault();
      return;
    }

    // Create new paragraph
    const newP = document.createElement("p");
    newP.innerHTML = "<br>";

    // Insert after current paragraph
    if (currentNode.nodeName === "P") {
      currentNode.parentNode.insertBefore(newP, currentNode.nextSibling);
    } else {
      // Find parent paragraph
      let parentP = currentNode;
      while (
        parentP &&
        parentP.nodeName !== "P" &&
        parentP !== this.contentElement
      ) {
        parentP = parentP.parentNode;
      }

      if (parentP && parentP.nodeName === "P") {
        parentP.parentNode.insertBefore(newP, parentP.nextSibling);
      } else {
        this.contentElement.appendChild(newP);
      }
    }

    // Move cursor to new paragraph
    EditorUtils.setCaretPosition(newP);

    e.preventDefault();
  },
};

// ====================== IMAGE UPLOAD MANAGER ======================
const ImageUploadManager = {
  init: function () {
    this.container = document.querySelector(".image-upload-container");
    this.overlay = document.querySelector(".overlay");
    this.uploadInput = document.getElementById("image-upload");
    this.confirmBtn = document.getElementById("confirm-upload");
    this.cancelBtn = document.getElementById("cancel-upload");

    this.cancelBtn.addEventListener("click", () => this.close());
    this.confirmBtn.addEventListener("click", () => this.handleUpload());
  },

  open: function () {
    this.container.classList.add("visible");
    this.overlay.classList.add("visible");
    this.uploadInput.value = "";
  },

  close: function () {
    this.container.classList.remove("visible");
    this.overlay.classList.remove("visible");
  },

  handleUpload: function () {
    if (this.uploadInput.files.length > 0) {
      const file = this.uploadInput.files[0];
      const reader = new FileReader();

      reader.onload = (e) => {
        const img = document.createElement("img");
        img.src = e.target.result;

        // Insert at current cursor position
        const selection = window.getSelection();
        if (selection.rangeCount) {
          const range = selection.getRangeAt(0);
          range.insertNode(img);

          // Add new paragraph after image
          const p = document.createElement("p");
          p.innerHTML = "<br>";
          range.insertNode(p);

          // Move cursor to new paragraph
          EditorUtils.setCaretPosition(p);
        }
      };

      reader.readAsDataURL(file);
    }

    this.close();
  },
};

// ====================== CONTENT MANAGER ======================
const ContentManager = {
  init: function () {
    this.saveBtn = document.getElementById("save-btn");
    this.publishBtn = document.getElementById("publish-btn");
    this.titleElement = document.querySelector(".editor-title");
    this.contentElement = document.querySelector(".editor-content");
  },

  getContentData: function () {
    return {
      title: this.titleElement.textContent.trim(),
      content: this.contentElement.innerHTML,
      plainText: this.contentElement.textContent.trim(),
    };
  },

  validateContent: function () {
    const contentData = this.getContentData();

    if (!contentData.title) {
      this.titleElement.classList.add("empty");
      this.titleElement.focus();

      setTimeout(() => {
        this.titleElement.classList.remove("empty");
      }, 500);

      return { valid: false, message: "Please enter a title before saving" };
    }

    if (!contentData.plainText) {
      this.contentElement.focus();
      return { valid: false, message: "Content cannot be empty" };
    }

    return { valid: true };
  },
};

// ====================== INITIALIZE EDITOR ======================
document.addEventListener("DOMContentLoaded", function () {
  // Initialize all modules
  PlaceholderManager.setup(document.querySelector(".editor-content"));
  WordCountManager.init();
  TitleManager.init();
  SlashCommandManager.init();
  ParagraphManager.init();
  ImageUploadManager.init();
  ContentManager.init();
});
