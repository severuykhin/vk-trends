import moment from 'moment'

export default (date: string): string => {
    if (!date) return '';
    return moment(date).format('YYYY-MM-DDTHH:mm:ss');
}