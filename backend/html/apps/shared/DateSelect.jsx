import React, { useReducer, useState } from 'react'
import { DateRangeInput } from '@datepicker-react/styled'
import { DateFormatter } from '../formatters'

export default (props) => {

    const { onChange } = props;

    const [focusedInput, setFocusedInput] = useState(null);
    const [startDate, setStartDate] = useState(null);
    const [endDate, setEndDate] = useState(null);

    const onDatesChange = (data) => {
        setStartDate(data.startDate);
        setEndDate(data.endDate);
        setFocusedInput(data.focusedInput);

        if (!data.startDate && !data.endDate) {
            onChange({});
        }

        if ((data.startDate && data.endDate)) {
            onChange({
                range_start: DateFormatter(data.startDate),
                range_end: DateFormatter(data.endDate)
            });
        }
    }

    const onFocusChange = (fInput) => {
        setFocusedInput(fInput);
    }

    return (
        <DateRangeInput
            onDatesChange={onDatesChange}
            onFocusChange={onFocusChange}
            startDate={startDate} // Date or null
            endDate={endDate} // Date or null
            focusedInput={focusedInput} // START_DATE, END_DATE or null
            displayFormat="DD-MM-YYYY"
        />
    )
}