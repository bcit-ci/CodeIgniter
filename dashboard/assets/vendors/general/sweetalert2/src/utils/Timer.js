export default class Timer {
  constructor (callback, delay) {
    let id, started, remaining = delay
    this.running = false

    this.start = function () {
      if (!this.running) {
        this.running = true
        started = new Date()
        id = setTimeout(callback, remaining)
      }
      return remaining
    }

    this.stop = function () {
      if (this.running) {
        this.running = false
        clearTimeout(id)
        remaining -= new Date() - started
      }
      return remaining
    }

    this.increase = function (n) {
      const running = this.running
      if (running) {
        this.stop()
      }
      remaining += n
      if (running) {
        this.start()
      }
      return remaining
    }

    this.getTimerLeft = function () {
      if (this.running) {
        this.stop()
        this.start()
      }
      return remaining
    }

    this.isRunning = function () {
      return this.running
    }

    this.start()
  }
}
