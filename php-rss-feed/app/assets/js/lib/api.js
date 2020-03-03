import Custom from './custom'

import axios from 'axios'

export default class API {

    // --------------------------   Lists -----------------------------------------------

    static loadFeedList() {
        return axios.get('/api/feed/list')
            .then(response => {
                // JSON responses are automatically parsed.
                console.log(response.data);

                var articles = [];
                if (response.data.status === true && typeof response.data.articles !== 'undefined') {
                    if (response.data.articles.length > 0) {
                        for (let i = 0; i < response.data.articles.length; i++) {
                            articles.push(Custom.feed_item_from_response(response.data.articles[i]));
                        }
                    }
                }

                var summary = [];
                if (response.data.status === true && typeof response.data.summary !== 'undefined') {
                    let keys = Object.keys(response.data.summary);
                    for (let i = 0; i < keys.length; i++) {
                        summary.push({'word':keys[i],'counter':response.data.summary[keys[i]]});
                    }
                }
                return {'articles':articles, 'summary':summary};

            })
            .catch(e => {
                throw Error('API exception ' + e.message)
            })
    }
}
