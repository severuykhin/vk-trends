class GroupIndex {

    constructor() {
        this.DOMMap = {
            buttons: []
        };
    }

    init () {
        this.DOMMap.buttons = $('[data-role="group-index-btn"]');

        const self = this;

        this.DOMMap.buttons.on('click', function () {
            let vkGroupId = $(this).data('id');
            self.runProcess(vkGroupId);
        });
    }

    runProcess(vkGroupId) {
        $.ajax({
            url: `/backend/group/${vkGroupId}/process`,
            method: 'post',
            success: (resp) => {
                console.log(resp);
            },
            error: (e) => {
                console.log(e);
            }
        });
    }
}

export default GroupIndex;