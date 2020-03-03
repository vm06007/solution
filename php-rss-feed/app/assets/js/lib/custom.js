import moment from 'moment-timezone'

export default class Custom {

    static feed_item_from_response(item) {

        if (item === null || typeof item === 'undefined') {
            return {}
        }

        return {
            'id': item.id,
            'updated': this.datestamp(item.updated),
            'title':item.title,
            'unique_code':item.unique_code
        }
    }

    static datestamp(datetime) {
        if (typeof datetime !== 'undefined' && datetime !== null) {
            //let result =  moment.utc(moment.tz(datetime.date, datetime.timezone)).local().format('YYYY-MM-DD');
            //let result = moment.utc(moment.tz(datetime.date, datetime.timezone)).local();
            let result = moment.utc(datetime).local();
            return result;
        } else {
            return null;
        }
    }
}
