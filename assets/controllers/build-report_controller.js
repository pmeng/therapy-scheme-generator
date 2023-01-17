import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    connect() {
        //
    }

    suppressLabels(event) {
        const table = this.element.getElementsByTagName('table')[0];
        const rows = [].slice.call(table.querySelectorAll('tr'));

        const switchRowState = function (state) {
            rows
                .filter(function (row) {
                    return row.getAttribute('data-label-row') !== null;
                })
                .forEach(function (row) {
                    console.log(state);
                    row.style.display = state ? 'none' : '';
                    row.setAttribute('data-suppress-label', `${state}`);
                });
        };

        switchRowState(event.target.checked);
    }

    useExcerpt(event) {
        const table = this.element.getElementsByTagName('table')[0];
        const cells = [].slice.call(table.querySelectorAll('th, td'));

        const hideColumn = function (index) {
            cells
                .filter(function (cell) {
                    return cell.getAttribute('data-column-index') === `${index}`;
                })
                .forEach(function (cell) {
                    cell.style.display = 'none';
                    cell.setAttribute('data-shown', 'false');
                });
        };

        const showColumn = function (index) {
            cells
                .filter(function (cell) {
                    return cell.getAttribute('data-column-index') === `${index}`;
                })
                .forEach(function (cell) {
                    cell.style.display = '';
                    cell.setAttribute('data-shown', 'true');
                });
        };

        if (event.target.checked) {
            hideColumn(1);
            showColumn(2);
        } else {
            hideColumn(2);
            showColumn(1);
        }
    }
}