import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static values = {
        targetUrl: String,
    }

    async deleteLabel(event) {
        const response = await fetch(`${this.targetUrlValue}`, {
            method: 'PUT',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });
        const data = await response.json();

        if (data.success) {
            location.href = data.redirect;
        }
    }
}