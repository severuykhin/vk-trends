import React, { Component } from "react";
import Chart from "react-apexcharts";

export default class Keywords extends Component {
    constructor(props) {
        super(props);
    }

    processData(data) {
        const categories = [];
        const values = [];

        data.forEach(el => {
            categories.push(el.key);
            values.push(el.value);
        });

        return { categories, values };
    }

    render() {

        const { data } = this.props;

        if (!data || data.length <= 0) {
            return null;
        }

        const { categories, values } = this.processData(data);

        const options = {
            chart: {
              id: "basic-bar"
            },
            xaxis: {
              categories: categories
            }
        };

        const series = [
            {
              name: "series-1",
              data: values
            }
        ];

        return (
            <div className="mixed-chart">
                <Chart
                    options={options}
                    series={series}
                    type="bar"
                    width="100%"
                />
            </div>
        );
    }
}