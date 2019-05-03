import range from '../core/range';

export default class History {
  constructor($editable) {
    this.stack = [];
    this.stackOffset = -1;
    this.$editable = $editable;
    this.editable = $editable[0];
  }

  makeSnapshot() {
    const rng = range.create(this.editable);
    const emptyBookmark = { s: { path: [], offset: 0 }, e: { path: [], offset: 0 } };

    return {
      contents: this.$editable.html(),
      bookmark: (rng ? rng.bookmark(this.editable) : emptyBookmark)
    };
  }

  applySnapshot(snapshot) {
    if (snapshot.contents !== null) {
      this.$editable.html(snapshot.contents);
    }
    if (snapshot.bookmark !== null) {
      range.createFromBookmark(this.editable, snapshot.bookmark).select();
    }
  }

  /**
  * @method rewind
  * Rewinds the history stack back to the first snapshot taken.
  * Leaves the stack intact, so that "Redo" can still be used.
  */
  rewind() {
    // Create snap shot if not yet recorded
    if (this.$editable.html() !== this.stack[this.stackOffset].contents) {
      this.recordUndo();
    }

    // Return to the first available snapshot.
    this.stackOffset = 0;

    // Apply that snapshot.
    this.applySnapshot(this.stack[this.stackOffset]);
  }

  /**
  *  @method commit
  *  Resets history stack, but keeps current editor's content.
  */
  commit() {
    // Clear the stack.
    this.stack = [];

    // Restore stackOffset to its original value.
    this.stackOffset = -1;

    // Record our first snapshot (of nothing).
    this.recordUndo();
  }

  /**
  * @method reset
  * Resets the history stack completely; reverting to an empty editor.
  */
  reset() {
    // Clear the stack.
    this.stack = [];

    // Restore stackOffset to its original value.
    this.stackOffset = -1;

    // Clear the editable area.
    this.$editable.html('');

    // Record our first snapshot (of nothing).
    this.recordUndo();
  }

  /**
   * undo
   */
  undo() {
    // Create snap shot if not yet recorded
    if (this.$editable.html() !== this.stack[this.stackOffset].contents) {
      this.recordUndo();
    }

    if (this.stackOffset > 0) {
      this.stackOffset--;
      this.applySnapshot(this.stack[this.stackOffset]);
    }
  }

  /**
   * redo
   */
  redo() {
    if (this.stack.length - 1 > this.stackOffset) {
      this.stackOffset++;
      this.applySnapshot(this.stack[this.stackOffset]);
    }
  }

  /**
   * recorded undo
   */
  recordUndo() {
    this.stackOffset++;

    // Wash out stack after stackOffset
    if (this.stack.length > this.stackOffset) {
      this.stack = this.stack.slice(0, this.stackOffset);
    }

    // Create new snapshot and push it to the end
    this.stack.push(this.makeSnapshot());
  }
}
