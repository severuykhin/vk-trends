class RabbitStatus {
    init() {
        this.container = $('[data-role="rabbit-status"]');

        if (this.container.length < 0) {
            return false;
        }

        this.render();
    }

    consume(message) {
        if (message.items && message.items.length > 0) {
            this.render(message.items);
        }
    }

    render(items = []) {
        let content = '';

        items.forEach(item => {
            content += `
                <div class="rabbit-status-row">
                    <div class="rabbit-status-name">${item.name}: &nbsp;</div>
                    <div class="rabbit-status-count">${item.messages}</div>
                </div>
            `;
        });

        this.container.html(content);
    }
}

export default RabbitStatus;