import moment from 'moment'

export default (date: string): string => {
    return moment(date).format('YYYY-MM-DDTHH:mm:ss');
}