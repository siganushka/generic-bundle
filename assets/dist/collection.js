import { Controller } from '@hotwired/stimulus';

/* stimulusFetch: 'lazy' */
export default class extends Controller {
  static targets = ['entry']

  add(event) {
    const detail = { button: event.target, element: this.element }
    const dispatched = this.dispatch('before-add', { detail, cancelable: true })
    if (dispatched.defaultPrevented) {
      return
    }

    const { prototype, prototypeName, index } = this.element.dataset
    const entry = prototype.replace(new RegExp(prototypeName, 'g'), index)

    const lastEntry = this.entryTargets.pop()
    if (lastEntry) {
      lastEntry.insertAdjacentHTML('afterend', entry.trim())
    } else {
      const container = this.element.querySelector('tbody') || this.element
      container.insertAdjacentHTML('afterbegin', entry.trim())
    }

    this.element.dataset.index ++
    this.dispatch('added', { detail })
  }

  delete(event) {
    const entryEl = event.target.closest(`[data-${this.identifier}-target=entry]`)
    if (this.entryTargets.includes(entryEl)) {
      const detail = { button: event.target, element: this.element }
      const dispatched = this.dispatch('before-delete', { detail, cancelable: true })
      if (dispatched.defaultPrevented) {
        return
      }

      entryEl.remove()
      this.dispatch('deleted', { detail })
    }
  }
}
