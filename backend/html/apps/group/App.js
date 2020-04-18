import React, { useState } from 'react'
import Keywords from '../shared/Keywords'
import DateSelect from '../shared/DateSelect'
import axios from 'axios'
import { QueryToObject } from '../formatters'

export default (props) => {

    const { data } = props;

    const [comments_verbal_portrait, set_comments_verbal_portrait] = useState(data.comments_verbal_portrait);

    const dateChange = async (data) => {

        var currentQuery = QueryToObject();

        data.id = currentQuery.id;

        var query = Object.keys(data)
            .map(k => encodeURIComponent(k) + '=' + encodeURIComponent(data[k]))
            .join('&');


        const resp = await axios.get(`/backend/api/group/vportrait?${query}`);

        if (resp.data.result === 'success') {
            set_comments_verbal_portrait(resp.data.payload);
        }

    }   

    return <div>
        <div style={{zIndex: 2, position:'relative'}} className="block">
            <DateSelect onChange={dateChange}/>
        </div>
        <div className="block">
            <Keywords data={comments_verbal_portrait}/>
        </div>
    </div>
}