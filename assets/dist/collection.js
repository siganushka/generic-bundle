import { Controller } from '@hotwired/stimulus';

/* stimulusFetch: 'lazy' */
export default class extends Controller {
  static targets = ['collection', 'entry']

  add() {
    const { prototype, prototypeName, index } = this.collectionTarget.dataset
    const entry = prototype.replace(new RegExp(prototypeName, 'g'), index)

    const containerEl = 'TABLE' === this.collectionTarget.tagName
      ? (this.collectionTarget.querySelector('tbody') || this.collectionTarget)
      : this.collectionTarget

    containerEl.insertAdjacentHTML('beforeend', entry.trim())
    this.collectionTarget.dataset.index ++
  }

  delete(event) {
    const entryEl = event.target.closest(`[data-${this.identifier}-target=entry]`)
    if (this.entryTargets.includes(entryEl)) {
      entryEl.remove()
    }
  }
}
